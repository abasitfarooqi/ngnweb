<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\PurchaseUsedVehicle;
use App\Support\FluxAdminFormPayload;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class PurchaseUsedForm extends Component
{
    use WithAuthorization;

    public ?PurchaseUsedVehicle $purchaseUsed = null;

    public array $form = [];

    public function mount(?PurchaseUsedVehicle $purchaseUsed = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->purchaseUsed = $purchaseUsed;

        if ($purchaseUsed && $purchaseUsed->exists) {
            $attrs = $purchaseUsed->getAttributes();
            if (! empty($attrs['purchase_date'])) {
                try {
                    $attrs['purchase_date'] = Carbon::parse($attrs['purchase_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['purchase_date'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'purchase_date' => now()->format('Y-m-d'),
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.purchase_date'  => ['nullable', 'date'],
            'form.full_name'      => ['required', 'string', 'max:255'],
            'form.phone_number'   => ['nullable', 'string', 'max:50'],
            'form.email'          => ['nullable', 'email', 'max:255'],
            'form.address'        => ['nullable', 'string', 'max:1000'],
            'form.postcode'       => ['nullable', 'string', 'max:20'],
            'form.make'           => ['nullable', 'string', 'max:100'],
            'form.model'          => ['nullable', 'string', 'max:100'],
            'form.year'           => ['nullable', 'string', 'max:10'],
            'form.colour'         => ['nullable', 'string', 'max:50'],
            'form.reg_no'         => ['nullable', 'string', 'max:20'],
            'form.vin'            => ['nullable', 'string', 'max:50'],
            'form.price'          => ['nullable', 'numeric', 'min:0'],
            'form.deposit'        => ['nullable', 'numeric', 'min:0'],
            'form.outstanding'    => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = FluxAdminFormPayload::onlyPersistable(PurchaseUsedVehicle::class, $data['form']);

        if ($this->purchaseUsed && $this->purchaseUsed->exists) {
            $this->purchaseUsed->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Record updated.');
        } else {
            $payload['user_id'] = FluxAdminFormPayload::adminUserId();
            PurchaseUsedVehicle::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Record created.');
        }

        $this->redirect(route('flux-admin.used-purchases.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.purchase-used-form');
    }
}
