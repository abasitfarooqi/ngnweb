<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnPartner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Partner — Flux Admin')]
class PartnerForm extends Component
{
    use WithAuthorization;

    public ?NgnPartner $partner = null;

    public array $form = [];

    public function mount(?NgnPartner $partner = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->partner = $partner?->id ? $partner : null;

        if ($this->partner) {
            $this->form = $this->partner->getAttributes();
        } else {
            $this->form = ['is_approved' => false];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.companyname'       => ['required', 'string', 'max:255'],
            'form.company_address'   => ['nullable', 'string', 'max:500'],
            'form.company_number'    => ['nullable', 'string', 'max:50'],
            'form.first_name'        => ['nullable', 'string', 'max:120'],
            'form.last_name'         => ['nullable', 'string', 'max:120'],
            'form.phone'             => ['nullable', 'string', 'max:40'],
            'form.mobile'            => ['nullable', 'string', 'max:40'],
            'form.email'             => ['nullable', 'email', 'max:255'],
            'form.website'           => ['nullable', 'string', 'max:255'],
            'form.fleet_size'        => ['nullable', 'integer', 'min:0'],
            'form.operating_since'   => ['nullable', 'date'],
            'form.is_approved'       => ['boolean'],
        ]);

        $payload = [
            'companyname'     => $this->form['companyname'],
            'company_address' => $this->form['company_address'] ?? null,
            'company_number'  => $this->form['company_number'] ?? null,
            'first_name'      => $this->form['first_name'] ?? null,
            'last_name'       => $this->form['last_name'] ?? null,
            'phone'           => $this->form['phone'] ?? null,
            'mobile'          => $this->form['mobile'] ?? null,
            'email'           => $this->form['email'] ?? null,
            'website'         => $this->form['website'] ?? null,
            'fleet_size'      => $this->form['fleet_size'] ?? null,
            'operating_since' => $this->form['operating_since'] ?: null,
            'is_approved'     => (bool) ($this->form['is_approved'] ?? false),
        ];

        if ($this->partner) {
            $this->partner->update($payload);
        } else {
            NgnPartner::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Partner saved.');
        $this->redirect(route('flux-admin.inventory-partners.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.inventory.partner-form');
    }
}
