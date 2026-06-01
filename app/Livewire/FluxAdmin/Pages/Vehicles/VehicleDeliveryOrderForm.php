<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\DeliveryVehicleType;
use App\Models\VehicleDeliveryOrder;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class VehicleDeliveryOrderForm extends Component
{
    use WithAuthorization;

    public ?VehicleDeliveryOrder $vehicleDeliveryOrder = null;

    public array $form = [];

    public function mount(?VehicleDeliveryOrder $vehicleDeliveryOrder = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->vehicleDeliveryOrder = $vehicleDeliveryOrder;

        if ($vehicleDeliveryOrder && $vehicleDeliveryOrder->exists) {
            $attrs = $vehicleDeliveryOrder->getAttributes();
            foreach (['quote_date', 'pickup_date'] as $field) {
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
                'quote_date' => now()->toDateString(),
                'user_id'    => auth()->id(),
                'surcharge'  => 0,
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.quote_date'                => ['required', 'date'],
            'form.pickup_date'               => ['nullable', 'date'],
            'form.total_distance'            => ['nullable', 'numeric', 'min:0'],
            'form.surcharge'                 => ['nullable', 'numeric', 'min:0'],
            'form.delivery_vehicle_type_id'  => ['required', 'integer', 'exists:delivery_vehicle_types,id'],
            'form.branch_id'                 => ['nullable', 'integer', 'exists:branches,id'],
            'form.vrm'                       => ['nullable', 'string', 'max:20'],
            'form.full_name'                 => ['required', 'string', 'max:255'],
            'form.phone_number'              => ['nullable', 'string', 'max:40'],
            'form.email'                     => ['nullable', 'email', 'max:255'],
            'form.notes'                     => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];
        $payload['user_id'] = $payload['user_id'] ?? auth()->id();

        if ($this->vehicleDeliveryOrder && $this->vehicleDeliveryOrder->exists) {
            $this->vehicleDeliveryOrder->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Delivery order updated.');
        } else {
            VehicleDeliveryOrder::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Delivery order created.');
        }

        $this->redirect(route('flux-admin.vehicle-delivery-orders.index'), navigate: true);
    }

    public function render()
    {
        $types    = DeliveryVehicleType::orderBy('name')->get(['id', 'name']);
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.vehicles.vehicle-delivery-order-form', compact('types', 'branches'));
    }
}
