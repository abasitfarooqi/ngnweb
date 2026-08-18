<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\CompanyVehicle;
use App\Models\Motorbike;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class CompanyVehicleForm extends Component
{
    use WithAuthorization;

    public ?CompanyVehicle $companyVehicle = null;

    public array $form = [];

    public string $motorbikeSearch = '';

    public array $motorbikeSuggestions = [];

    public function mount(?CompanyVehicle $companyVehicle = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->companyVehicle = $companyVehicle;

        if ($companyVehicle && $companyVehicle->exists) {
            $this->form = $companyVehicle->getAttributes();
            $this->motorbikeSearch = $companyVehicle->motorbike?->reg_no ?? '';
        } else {
            $this->form = [];
        }
    }

    public function updatingMotorbikeSearch(): void
    {
        if (strlen($this->motorbikeSearch) < 2) {
            $this->motorbikeSuggestions = [];

            return;
        }

        $this->motorbikeSuggestions = Motorbike::query()
            ->where('reg_no', 'like', '%'.$this->motorbikeSearch.'%')
            ->limit(8)
            ->get(['id', 'reg_no'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'reg' => $m->reg_no,
            ])->toArray();
    }

    public function selectMotorbike(int $id, string $reg): void
    {
        $this->form['motorbike_id'] = $id;
        $this->motorbikeSearch = $reg;
        $this->motorbikeSuggestions = [];
    }

    public function commitMotorbikeSearch(): void
    {
        if (! empty($this->form['motorbike_id'])) {
            return;
        }

        if ($this->motorbikeSuggestions === [] && strlen($this->motorbikeSearch) >= 2) {
            $this->updatingMotorbikeSearch();
        }

        if ($this->motorbikeSuggestions === []) {
            return;
        }

        $compact = strtoupper(preg_replace('/\s+/', '', $this->motorbikeSearch) ?? '');
        foreach ($this->motorbikeSuggestions as $suggestion) {
            $reg = strtoupper(preg_replace('/\s+/', '', (string) ($suggestion['reg'] ?? '')) ?? '');
            if ($compact !== '' && $reg === $compact) {
                $this->selectMotorbike((int) $suggestion['id'], (string) $suggestion['reg']);

                return;
            }
        }

        if (count($this->motorbikeSuggestions) === 1) {
            $first = $this->motorbikeSuggestions[0];
            $this->selectMotorbike((int) $first['id'], (string) $first['reg']);
        }
    }

    protected function formRules(): array
    {
        return [
            'form.custodian'    => ['required', 'string', 'max:255'],
            'form.motorbike_id' => ['required', 'integer', Rule::unique('company_vehicles', 'motorbike_id')->ignore($this->companyVehicle?->id)],
        ];
    }

    public function save(): void
    {
        $this->commitMotorbikeSearch();

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
