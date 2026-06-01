<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMemberPurchase;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Purchase — Flux Admin')]
class PurchaseForm extends Component
{
    use WithAuthorization;

    public ?ClubMemberPurchase $purchase = null;

    public array $form = [];

    public function mount(?ClubMemberPurchase $purchase = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-club');
        $this->purchase = $purchase?->id ? $purchase : null;

        if ($this->purchase) {
            $this->form = $this->purchase->getAttributes();
            $this->form['date'] = $this->purchase->date ? Carbon::parse($this->purchase->date)->format('Y-m-d') : null;
        } else {
            $this->form = ['date' => now()->toDateString(), 'is_redeemed' => false];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.date'           => ['required', 'date'],
            'form.club_member_id' => ['required', 'integer'],
            'form.pos_invoice'    => ['nullable', 'string', 'max:255'],
            'form.percent'        => ['nullable', 'numeric'],
            'form.total'          => ['required', 'numeric'],
            'form.discount'       => ['nullable', 'numeric'],
            'form.redeem_amount'  => ['nullable', 'numeric'],
            'form.branch_id'      => ['nullable', 'integer'],
            'form.is_redeemed'    => ['boolean'],
        ]);

        $payload = [
            'date'           => $this->form['date'],
            'club_member_id' => $this->form['club_member_id'],
            'pos_invoice'    => $this->form['pos_invoice'] ?? null,
            'percent'        => $this->form['percent'] ?? null,
            'total'          => $this->form['total'],
            'discount'       => $this->form['discount'] ?? null,
            'redeem_amount'  => $this->form['redeem_amount'] ?? null,
            'branch_id'      => $this->form['branch_id'] ?: null,
            'is_redeemed'    => (bool) ($this->form['is_redeemed'] ?? false),
        ];

        if ($this->purchase) {
            $this->purchase->update($payload);
        } else {
            ClubMemberPurchase::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Purchase saved.');
        $this->redirect(route('flux-admin.club-purchases.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.purchase-form');
    }
}
