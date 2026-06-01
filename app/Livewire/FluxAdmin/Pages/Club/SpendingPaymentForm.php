<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMemberSpendingPayment;
use Carbon\Carbon;
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

    public function mount(?ClubMemberSpendingPayment $spendingPayment = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-club');
        $this->spendingPayment = $spendingPayment?->id ? $spendingPayment : null;

        if ($this->spendingPayment) {
            $this->form = $this->spendingPayment->getAttributes();
            $this->form['date'] = $this->spendingPayment->date ? Carbon::parse($this->spendingPayment->date)->format('Y-m-d') : null;
        } else {
            $this->form = ['date' => now()->toDateString()];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.date'           => ['required', 'date'],
            'form.club_member_id' => ['required', 'integer'],
            'form.spending_id'    => ['nullable', 'integer'],
            'form.pos_invoice'    => ['nullable', 'string', 'max:255'],
            'form.received_total' => ['required', 'numeric'],
            'form.branch_id'      => ['nullable', 'integer'],
            'form.note'           => ['nullable', 'string'],
        ]);

        $payload = [
            'date'           => $this->form['date'],
            'club_member_id' => $this->form['club_member_id'],
            'spending_id'    => $this->form['spending_id'] ?: null,
            'pos_invoice'    => $this->form['pos_invoice'] ?? null,
            'received_total' => $this->form['received_total'],
            'branch_id'      => $this->form['branch_id'] ?: null,
            'note'           => $this->form['note'] ?? null,
        ];

        if ($this->spendingPayment) {
            $this->spendingPayment->update($payload);
        } else {
            ClubMemberSpendingPayment::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Payment saved.');
        $this->redirect(route('flux-admin.club-spending-payments.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.spending-payment-form');
    }
}
