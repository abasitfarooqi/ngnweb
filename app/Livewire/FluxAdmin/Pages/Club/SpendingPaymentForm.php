<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMember;
use App\Models\ClubMemberSpending;
use App\Models\ClubMemberSpendingPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Spending Payment — Flux Admin')]
class SpendingPaymentForm extends Component
{
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
        } else {
            $this->form = ['date' => now()->toDateString(), 'include_today' => true];
        }

        $this->refreshSpendingTotals();
    }

    public function updatedFormClubMemberId(): void
    {
        $this->refreshSpendingTotals();
    }

    public function save(): void
    {
        $this->validate([
            'form.date'           => ['required', 'date'],
            'form.club_member_id' => ['required', 'integer', 'exists:club_members,id'],
            'form.spending_id'    => ['nullable', 'integer', 'exists:club_member_spendings,id'],
            'form.pos_invoice'    => ['nullable', 'string', 'max:50'],
            'form.received_total' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail): void {
                    $member = ClubMember::find($this->form['club_member_id'] ?? null);
                    if ($member && (float) $value > (float) $member->total_unpaid_spending) {
                        $fail("The payment amount (£{$value}) exceeds total unpaid amount (£{$member->total_unpaid_spending}).");
                    }
                },
            ],
            'form.branch_id'      => ['required', 'string', 'in:CATFORD,SUTTON,TOOTING'],
            'form.note'           => ['nullable', 'string', 'max:255'],
            'form.include_today'  => ['boolean'],
        ]);

        $payload = [
            'date'           => $this->form['date'],
            'club_member_id' => $this->form['club_member_id'],
            'spending_id'    => $this->form['spending_id'] ?: null,
            'pos_invoice'    => $this->form['pos_invoice'] ?? null,
            'received_total' => $this->form['received_total'],
            'branch_id'      => $this->form['branch_id'],
            'note'           => $this->form['note'] ?? null,
            'user_id'        => auth()->id(),
        ];

        DB::transaction(function () use ($payload): void {
            if ($this->spendingPayment) {
                $original = $this->spendingPayment->replicate();
                $this->revertPayment($original);
                $this->spendingPayment->update($payload);
                $entry = $this->spendingPayment->refresh();
            } else {
                $entry = ClubMemberSpendingPayment::create($payload);
            }

            $this->applyPaymentToSpendings($entry);
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

    protected function applyPaymentToSpendings(ClubMemberSpendingPayment $entry): void
    {
        $remainingPayment = round((float) $entry->received_total, 2);
        if ($remainingPayment <= 0) {
            return;
        }

        $affectedSpendings = [];
        $query = ClubMemberSpending::query()
            ->where('club_member_id', $entry->club_member_id)
            ->where(function ($q): void {
                $q->where('is_paid', false)
                    ->orWhere(function ($subQ): void {
                        $subQ->where('is_paid', true)
                            ->whereRaw('ROUND(total - COALESCE(paid_amount, 0), 2) > 0.01');
                    });
            });

        if ($entry->spending_id) {
            $query->where('id', $entry->spending_id);
        }

        if ($entry->spending_id && empty($this->form['include_today'])) {
            $query->whereDate('date', '<>', now()->toDateString());
        }

        $spendings = $query->orderBy('date')->orderBy('id')->get();

        foreach ($spendings as $spending) {
            if ($remainingPayment <= 0) {
                break;
            }

            $paidAmount = round((float) ($spending->paid_amount ?? 0), 2);
            $unpaidAmount = round((float) $spending->total - $paidAmount, 2);
            if ($unpaidAmount <= 0.01) {
                continue;
            }

            $appliedAmount = min($remainingPayment, $unpaidAmount);
            $newPaidAmount = round($paidAmount + $appliedAmount, 2);

            $spending->forceFill([
                'paid_amount' => $newPaidAmount,
                'is_paid' => round((float) $spending->total - $newPaidAmount, 2) <= 0.01,
            ])->save();

            $affectedSpendings[] = $spending->id;
            $remainingPayment = round($remainingPayment - $appliedAmount, 2);
        }

        if ($affectedSpendings) {
            $appliedTotal = round((float) $entry->received_total - $remainingPayment, 2);
            $note = 'Applied £'.number_format($appliedTotal, 2, '.', '').' using FIFO to spending IDs: '.implode(', ', $affectedSpendings);
            if ($remainingPayment > 0) {
                $note .= '. Remaining £'.number_format($remainingPayment, 2, '.', '').' could not be applied.';
            }

            $entry->forceFill([
                'note' => trim(($entry->note ?? '')."\n".$note),
                'spending_id' => null,
            ])->save();
        }
    }

    protected function revertPayment(ClubMemberSpendingPayment $payment): void
    {
        $remainingRevert = round((float) ($payment->received_total ?? 0), 2);
        if ($remainingRevert <= 0) {
            return;
        }

        $spendings = ClubMemberSpending::query()
            ->where('club_member_id', $payment->club_member_id)
            ->where('paid_amount', '>', 0)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        foreach ($spendings as $spending) {
            if ($remainingRevert <= 0) {
                break;
            }

            $currentPaid = round((float) ($spending->paid_amount ?? 0), 2);
            $revertAmount = min($remainingRevert, $currentPaid);
            $newPaid = max(0, round($currentPaid - $revertAmount, 2));

            $spending->forceFill([
                'paid_amount' => $newPaid,
                'is_paid' => round((float) $spending->total - $newPaid, 2) <= 0.01,
            ])->save();

            $remainingRevert = round($remainingRevert - $revertAmount, 2);
        }
    }
}
