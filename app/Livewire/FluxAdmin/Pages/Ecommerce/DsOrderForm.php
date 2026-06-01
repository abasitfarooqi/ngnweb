<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\DsOrder;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('DS Order — Flux Admin')]
class DsOrderForm extends Component
{
    use WithAuthorization;

    public ?DsOrder $dsOrder = null;

    public array $form = [];

    public function mount(?DsOrder $dsOrder = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-ecommerce');
        $this->dsOrder = $dsOrder?->id ? $dsOrder : null;

        if ($this->dsOrder) {
            $attrs = $this->dsOrder->getAttributes();
            $attrs['pick_up_datetime'] = $this->dsOrder->pick_up_datetime
                ? Carbon::parse($this->dsOrder->pick_up_datetime)->format('Y-m-d')
                : null;
            $this->form = $attrs;
        } else {
            $this->form = ['pick_up_datetime' => now()->toDateString(), 'proceed' => false];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.pick_up_datetime' => ['required', 'date'],
            'form.full_name'        => ['required', 'string', 'max:255'],
            'form.phone'            => ['nullable', 'string', 'max:50'],
            'form.address'          => ['nullable', 'string'],
            'form.postcode'         => ['nullable', 'string', 'max:20'],
            'form.note'             => ['nullable', 'string'],
            'form.proceed'          => ['boolean'],
        ]);

        $payload = [
            'pick_up_datetime' => $this->form['pick_up_datetime'],
            'full_name'        => $this->form['full_name'],
            'phone'            => $this->form['phone'] ?? null,
            'address'          => $this->form['address'] ?? null,
            'postcode'         => $this->form['postcode'] ?? null,
            'note'             => $this->form['note'] ?? null,
            'proceed'          => (bool) ($this->form['proceed'] ?? false),
        ];

        if ($this->dsOrder) {
            $this->dsOrder->update($payload);
        } else {
            DsOrder::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Order saved.');
        $this->redirect(route('flux-admin.ds-orders.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.ecommerce.ds-order-form');
    }
}
