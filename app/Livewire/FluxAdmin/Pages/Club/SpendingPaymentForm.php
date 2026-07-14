<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\SearchesClubMembers;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMember;
use App\Models\ClubMemberSpendingPayment;
use App\Services\ClubSpendingPaymentAllocator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Spending Payment — Flux Admin')]
class SpendingPaymentForm extends Component
{
    use SearchesClubMembers;
    use WithAuthorization;

    public ?ClubMemberSpendingPayment $spendingPayment = null;

    public array $form = [];

    public ?float $totalSpending = null;

    public ?float $totalUnpaid = null;

    public function mount(?ClubMemberSpendingPayment $spendingPayment = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-club');
        $this->spendingPayment = $spendingPayment?->id ? $spendingPayment : null;

        if ($this->spendingPayment) {
            $this->form = $this->spendingPayment->getAttributes();
            $this->form['date'] = $this->spendingPayment->date ? Carbon::parse($this->spendingPayment->date)->format('Y-m-d') : null;
            $this->form['include_today'] = true;
            $this->fillMemberSearchLabel((int) $this->spendingPayment->club_member_id);
        } else {
            $this->form = [
                'date' => now()->toDateString(),
                'include_today' => true,
                'branch_id' => '',
                'received_total' => '',
                'spending_id' => null,
            ];
        }

        $this->refreshSpendingTotals();
    }

    public function onClubMemberSelected(ClubMember $member): void
    {
        $this->refreshSpendingTotals();
    }

    public function updatedFormClubMemberId(): void
    {
        $this->refreshSpendingTotals();
    }

    public function save(ClubSpendingPaymentAllocator $allocator): void
    {
        $this->validate([
            'form.date' => ['required', 'date'],
            'form.club_member_id' => ['required', 'integer', 'exists:club_members,id'],
            'form.pos_invoice' => ['nullable', 'string', 'max:50'],
            'form.received_total' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail): void {
                    $member = ClubMember::find($this->form['club_member_id'] ?? null);
                    if (! $member) {
                        return;
                    }
                    $available = (float) $member->total_unpaid_spending;
                    if ($this->spendingPayment) {
                        $available += (float) $this->spendingPayment->received_total;
                    }
                    if ((float) $value > round($available, 2) + 0.001) {
                        $fail("The payment amount (£{$value}) exceeds total unpaid amount (£".number_format($available, 2).').');
                    }
                },
            ],
            'form.branch_id' => ['required', 'string', 'in:CATFORD,SUTTON,TOOTING'],
            'form.note' => ['nullable', 'string', 'max:255'],
            'form.include_today' => ['boolean'],
        ]);

        $payload = [
            'date' => $this->form['date'],
            'club_member_id' => $this->form['club_member_id'],
            'spending_id' => null, // FIFO across unpaid spendings (Backpack behaviour)
            'pos_invoice' => $this->form['pos_invoice'] ?: null,
            'received_total' => $this->form['received_total'],
            'branch_id' => $this->form['branch_id'],
            'note' => $this->form['note'] ?? null,
            'user_id' => backpack_user()?->id ?? auth()->id(),
        ];

        DB::transaction(function () use ($payload, $allocator): void {
            if ($this->spendingPayment) {
                $original = $this->spendingPayment->replicate();
                $allocator->revert($original);
                $this->spendingPayment->update($payload);
                $entry = $this->spendingPayment->refresh();
            } else {
                $entry = ClubMemberSpendingPayment::create($payload);
            }

            $allocator->apply($entry, (bool) ($this->form['include_today'] ?? true));
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Payment saved.');
        $this->redirect(route('flux-admin.club-spending-payments.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.spending-payment-form');
    }

    protected function refreshSpendingTotals(): void
    {
        $memberId = (int) ($this->form['club_member_id'] ?? 0);
        $member = $memberId > 0 ? ClubMember::find($memberId) : null;

        $this->totalSpending = $member ? round((float) $member->total_spending, 2) : null;
        $this->totalUnpaid = $member ? round((float) $member->total_unpaid_spending, 2) : null;
    }
}
