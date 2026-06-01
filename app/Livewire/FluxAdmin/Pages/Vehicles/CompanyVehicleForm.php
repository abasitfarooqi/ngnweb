<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\CompanyVehicle;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class CompanyVehicleForm extends Component
{
    use WithAuthorization;

    public ?CompanyVehicle $companyVehicle = null;

    public array $form = [];

    public function mount(?CompanyVehicle $companyVehicle = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->companyVehicle = $companyVehicle;

        if ($companyVehicle && $companyVehicle->exists) {
            $this->form = $companyVehicle->getAttributes();
        } else {
            $this->form = [];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.custodian'    => ['required', 'string', 'max:255'],
            'form.motorbike_id' => ['required', 'integer'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->companyVehicle && $this->companyVehicle->exists) {
            $this->companyVehicle->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Company vehicle updated.');
        } else {
            CompanyVehicle::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Company vehicle created.');
        }

        $this->redirect(route('flux-admin.company-vehicles.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.company-vehicle-form');
    }
}
