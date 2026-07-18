<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use App\Models\Ecommerce\EcOrder;
use App\Models\Ecommerce\EcPaymentMethod;
use App\Models\Ecommerce\EcShippingMethod;
use App\Support\FluxAdminFormPayload;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
            $ecOrder->load(['orderItems', 'customer.customer', 'branch', 'shippingMethod', 'paymentMethod', 'customerAddress']);
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

    public function updatedFormCustomerId($value): void
    {
        if (! $value) {
            $this->form['customer_address_id'] = null;

            return;
        }

        $auth = CustomerAuth::query()->with('customer')->find($value);
        if (! $auth?->customer_id) {
            return;
        }

        $defaultAddressId = CustomerAddress::query()
            ->where('customer_id', $auth->customer_id)
            ->orderByDesc('is_default')
            ->value('id');

        if ($defaultAddressId && empty($this->form['customer_address_id'])) {
            $this->form['customer_address_id'] = $defaultAddressId;
        }
    }

    protected function formRules(): array
    {
        return [
            'form.order_date'          => ['required', 'date'],
            'form.customer_id'         => ['nullable', 'integer', 'exists:customer_auths,id'],
            'form.branch_id'           => ['nullable', 'integer', 'exists:branches,id'],
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
            'form.shipping_method_id'  => ['nullable', 'integer', 'exists:ec_shipping_methods,id'],
            'form.payment_method_id'   => ['nullable', 'integer', 'exists:ec_payment_methods,id'],
            'form.customer_address_id' => ['nullable', 'integer', 'exists:customer_addresses,id'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = FluxAdminFormPayload::onlyPersistable(EcOrder::class, $data['form']);

        if ($this->ecOrder && $this->ecOrder->exists) {
            $this->ecOrder->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Order updated.');
        } else {
            EcOrder::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Order created.');
        }

        $this->redirect(route('flux-admin.ec-orders.index'), navigate: true);
    }

    protected function customerAddresses(): Collection
    {
        $customerAuthId = $this->form['customer_id'] ?? null;
        if (! $customerAuthId) {
            return collect();
        }

        $auth = CustomerAuth::query()->find($customerAuthId);
        if (! $auth?->customer_id) {
            return collect();
        }

        return CustomerAddress::query()
            ->where('customer_id', $auth->customer_id)
            ->orderByDesc('is_default')
            ->get(['id', 'first_name', 'last_name', 'street_address', 'street_address_plus', 'postcode', 'city']);
    }

    public function render()
    {
        $customerAuths = CustomerAuth::query()
            ->with('customer:id,first_name,last_name,phone,email')
            ->orderBy('email')
            ->limit(500)
            ->get(['id', 'email', 'customer_id']);

        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);
        $shippingMethods = EcShippingMethod::query()->orderBy('name')->get(['id', 'name']);
        $paymentMethods = EcPaymentMethod::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.ecommerce.ec-order-form', [
            'customerAuths'     => $customerAuths,
            'branches'          => $branches,
            'shippingMethods'   => $shippingMethods,
            'paymentMethods'    => $paymentMethods,
            'customerAddresses' => $this->customerAddresses(),
            'orderItems'        => $this->ecOrder?->orderItems ?? collect(),
        ]);
    }
}
