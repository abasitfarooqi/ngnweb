<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\SearchesClubMembers;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMember;
use App\Models\ClubMemberSpending;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Spending — Flux Admin')]
class SpendingForm extends Component
{
    use SearchesClubMembers;
    use WithAuthorization;

    public ?ClubMemberSpending $spending = null;

    public array $form = [];

    public ?string $posInvoiceWarning = null;

    public function mount(?ClubMemberSpending $spending = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-club');
        $this->spending = $spending?->id ? $spending : null;

        if ($this->spending) {
            $this->form = $this->spending->getAttributes();
            $this->form['date'] = $this->spending->date ? Carbon::parse($this->spending->date)->format('Y-m-d') : null;
            $this->fillMemberSearchLabel((int) $this->spending->club_member_id);
        } else {
            $this->form = [
                'date' => now()->toDateString(),
                'total' => '',
                'branch_id' => '',
                'pos_invoice' => '',
            ];
        }

        $this->checkPosInvoice();
    }

    public function updatedFormPosInvoice(): void
    {
        $this->checkPosInvoice();
    }

    public function onClubMemberSelected(ClubMember $member): void
    {
        // no-op
    }

    public function save(): void
    {
        $rules = [
            'form.date' => ['required', 'date'],
            'form.club_member_id' => ['required', 'integer', 'exists:club_members,id'],
            'form.pos_invoice' => ['nullable', 'string', 'max:255'],
            'form.total' => ['required', 'numeric', 'min:0'],
            'form.branch_id' => ['nullable', 'string', 'in:CATFORD,SUTTON,TOOTING'],
        ];

        if (filled($this->form['pos_invoice'] ?? null)) {
            $unique = Rule::unique('club_member_spendings', 'pos_invoice');
            if ($this->spending?->id) {
                $unique = $unique->ignore($this->spending->id);
            }
            $rules['form.pos_invoice'][] = $unique;
        }

        $this->validate($rules);

        $payload = [
            'date' => $this->form['date'],
            'club_member_id' => $this->form['club_member_id'],
            'pos_invoice' => $this->form['pos_invoice'] ?: null,
            'total' => $this->form['total'],
            'branch_id' => $this->form['branch_id'] ?: null,
            'user_id' => backpack_user()?->id ?? auth()->id(),
        ];

        if ($this->spending) {
            // Preserve payment progress — paid_amount / is_paid come from spending payments.
            $this->spending->update($payload);
        } else {
            ClubMemberSpending::create(array_merge($payload, [
                'paid_amount' => 0,
                'is_paid' => false,
            ]));
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Spending saved.');
        $this->redirect(route('flux-admin.club-spending.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.spending-form');
    }

    protected function checkPosInvoice(): void
    {
        $invoice = trim((string) ($this->form['pos_invoice'] ?? ''));
        if ($invoice === '') {
            $this->posInvoiceWarning = null;

            return;
        }

        $query = ClubMemberSpending::where('pos_invoice', $invoice);
        if ($this->spending?->id) {
            $query->where('id', '!=', $this->spending->id);
        }

        $this->posInvoiceWarning = $query->exists()
            ? 'This POS invoice already exists on another spending record.'
            : null;
    }
}
