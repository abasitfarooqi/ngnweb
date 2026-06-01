<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMemberSpending;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Spending — Flux Admin')]
class SpendingForm extends Component
{
    use WithAuthorization;

    public ?ClubMemberSpending $spending = null;

    public array $form = [];

    public function mount(?ClubMemberSpending $spending = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-club');
        $this->spending = $spending?->id ? $spending : null;

        if ($this->spending) {
            $this->form = $this->spending->getAttributes();
            $this->form['date'] = $this->spending->date ? Carbon::parse($this->spending->date)->format('Y-m-d') : null;
        } else {
            $this->form = ['date' => now()->toDateString(), 'is_paid' => false];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.date'           => ['required', 'date'],
            'form.club_member_id' => ['required', 'integer'],
            'form.pos_invoice'    => ['nullable', 'string', 'max:255'],
            'form.total'          => ['required', 'numeric'],
            'form.paid_amount'    => ['nullable', 'numeric'],
            'form.branch_id'      => ['nullable', 'integer'],
            'form.is_paid'        => ['boolean'],
        ]);

        $payload = [
            'date'           => $this->form['date'],
            'club_member_id' => $this->form['club_member_id'],
            'pos_invoice'    => $this->form['pos_invoice'] ?? null,
            'total'          => $this->form['total'],
            'paid_amount'    => $this->form['paid_amount'] ?? null,
            'branch_id'      => $this->form['branch_id'] ?: null,
            'is_paid'        => (bool) ($this->form['is_paid'] ?? false),
        ];

        if ($this->spending) {
            $this->spending->update($payload);
        } else {
            ClubMemberSpending::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Spending saved.');
        $this->redirect(route('flux-admin.club-spending.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.spending-form');
    }
}
