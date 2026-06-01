<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Ecommerce\EcOrder;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class EcOrderForm extends Component
{
    use WithAuthorization;

    public ?EcOrder $ecOrder = null;

    public array $form = [];

    public function mount(?EcOrder $ecOrder = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-ecommerce');
        $this->ecOrder = $ecOrder;

        if ($ecOrder && $ecOrder->exists) {
            $attrs = $ecOrder->getAttributes();
            foreach (['order_date', 'shipping_date', 'payment_date'] as $field) {
                if (! empty($attrs[$field])) {
                    try {
                        $attrs[$field] = Carbon::parse($attrs[$field])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$field] = null;
                    }
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'order_date'      => now()->toDateString(),
                'order_status'    => 'pending',
                'payment_status'  => 'unpaid',
                'shipping_status' => 'pending',
                'currency'        => 'GBP',
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.order_date'          => ['required', 'date'],
            'form.customer_id'         => ['nullable', 'integer'],
            'form.branch_id'           => ['nullable', 'integer'],
            'form.order_status'        => ['required', 'string'],
            'form.total_amount'        => ['nullable', 'numeric'],
            'form.discount'            => ['nullable', 'numeric'],
            'form.tax'                 => ['nullable', 'numeric'],
            'form.grand_total'         => ['nullable', 'numeric'],
            'form.shipping_cost'       => ['nullable', 'numeric'],
            'form.shipping_status'     => ['nullable', 'string'],
            'form.shipping_date'       => ['nullable', 'date'],
            'form.payment_status'      => ['nullable', 'string'],
            'form.payment_date'        => ['nullable', 'date'],
            'form.payment_reference'   => ['nullable', 'string', 'max:255'],
            'form.currency'            => ['nullable', 'string', 'max:10'],
            'form.shipping_method_id'  => ['nullable', 'integer'],
            'form.payment_method_id'   => ['nullable', 'integer'],
            'form.customer_address_id' => ['nullable', 'integer'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->ecOrder && $this->ecOrder->exists) {
            $this->ecOrder->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Order updated.');
        } else {
            EcOrder::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Order created.');
        }

        $this->redirect(route('flux-admin.ec-orders.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.ecommerce.ec-order-form');
    }
}
