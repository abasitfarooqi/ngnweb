<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\DsOrder;
use App\Models\DsOrderItem;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class DsOrderItemForm extends Component
{
    use WithAuthorization;

    public ?DsOrderItem $dsOrderItem = null;

    public array $form = [];

    public function mount(?DsOrderItem $dsOrderItem = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->dsOrderItem = $dsOrderItem;

        if ($dsOrderItem && $dsOrderItem->exists) {
            $this->form = $dsOrderItem->getAttributes();
        } else {
            $this->form = ['moveable' => true, 'documents' => true, 'keys' => true];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.ds_order_id'       => ['required', 'integer', 'exists:ds_orders,id'],
            'form.vrm'               => ['nullable', 'string', 'max:20'],
            'form.pickup_address'    => ['required', 'string', 'max:500'],
            'form.pickup_postcode'   => ['required', 'string', 'max:20'],
            'form.dropoff_address'   => ['required', 'string', 'max:500'],
            'form.dropoff_postcode'  => ['required', 'string', 'max:20'],
            'form.pickup_lat'        => ['nullable', 'numeric'],
            'form.pickup_lon'        => ['nullable', 'numeric'],
            'form.dropoff_lat'       => ['nullable', 'numeric'],
            'form.dropoff_lon'       => ['nullable', 'numeric'],
            'form.moveable'          => ['nullable', 'boolean'],
            'form.documents'         => ['nullable', 'boolean'],
            'form.keys'              => ['nullable', 'boolean'],
            'form.distance'          => ['nullable', 'numeric', 'min:0'],
            'form.note'              => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->dsOrderItem && $this->dsOrderItem->exists) {
            $this->dsOrderItem->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Item updated.');
        } else {
            DsOrderItem::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Item created.');
        }

        $this->redirect(route('flux-admin.ds-order-items.index'), navigate: true);
    }

    public function render()
    {
        $dsOrders = DsOrder::query()
            ->latest('pick_up_datetime')
            ->limit(300)
            ->get(['id', 'full_name', 'phone', 'postcode', 'pick_up_datetime']);

        return view('flux-admin.pages.ecommerce.ds-order-item-form', compact('dsOrders'));
    }
}
