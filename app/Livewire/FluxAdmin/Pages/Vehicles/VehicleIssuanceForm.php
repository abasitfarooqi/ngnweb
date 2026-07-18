<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\VehicleIssuance;
use App\Support\FluxAdminFormPayload;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class VehicleIssuanceForm extends Component
{
    use WithAuthorization;

    public ?VehicleIssuance $vehicleIssuance = null;

    public array $form = [];

    public function mount(?VehicleIssuance $vehicleIssuance = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-renting-page');
        $this->vehicleIssuance = $vehicleIssuance;

        if ($vehicleIssuance && $vehicleIssuance->exists) {
            $attrs = $vehicleIssuance->getAttributes();
            if (! empty($attrs['issue_date'])) {
                try {
                    $attrs['issue_date'] = Carbon::parse($attrs['issue_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['issue_date'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'issue_date'  => now()->toDateString(),
                'user_id'     => FluxAdminFormPayload::adminUserId(),
                'is_returned' => false,
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.issue_date'   => ['required', 'date'],
            'form.user_id'      => ['required', 'integer'],
            'form.branch_id'    => ['nullable', 'integer'],
            'form.motorbike_id' => ['required', 'integer'],
            'form.customer_id'  => ['nullable', 'integer'],
            'form.notes'        => ['nullable', 'string'],
            'form.is_returned'  => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = FluxAdminFormPayload::onlyPersistable(VehicleIssuance::class, $data['form']);
        if (empty($payload['user_id'])) {
            $payload['user_id'] = FluxAdminFormPayload::adminUserId();
        }

        if ($this->vehicleIssuance && $this->vehicleIssuance->exists) {
            $this->vehicleIssuance->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Issuance updated.');
        } else {
            VehicleIssuance::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Issuance created.');
        }

        $this->redirect(route('flux-admin.vehicle-issuances.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.vehicle-issuance-form');
    }
}
