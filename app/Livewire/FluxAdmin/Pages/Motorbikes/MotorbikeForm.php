<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Motorbike;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike — Flux Admin')]
class MotorbikeForm extends Component
{
    use WithAuthorization;

    public ?int $motorbikeId = null;

    public array $form = [];

    public function mount(?Motorbike $motorbike = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');

        if ($motorbike !== null) {
            $this->motorbikeId = $motorbike->id;
            $attrs = $motorbike->getAttributes();
            $this->form = [
                'reg_no' => $attrs['reg_no'] ?? '',
                'make' => $attrs['make'] ?? '',
                'model' => $attrs['model'] ?? '',
                'year' => $attrs['year'] ?? '',
                'vin_number' => $attrs['vin_number'] ?? '',
                'engine' => $attrs['engine'] ?? '',
                'color' => $attrs['color'] ?? '',
                'fuel_type' => $attrs['fuel_type'] ?? 'Petrol',
                'is_ebike' => (bool) ($attrs['is_ebike'] ?? false),
                'branch_id' => $attrs['branch_id'] ?? '',
                'wheel_plan' => $attrs['wheel_plan'] ?? '2 Wheel',
                'type_approval' => $attrs['type_approval'] ?? 'L3',
                'vehicle_profile_id' => $attrs['vehicle_profile_id'] ?? '',
                'month_of_first_registration' => $this->formatDate($attrs['month_of_first_registration'] ?? null, 'Y-m-d'),
                'date_of_last_v5c_issuance' => $this->formatDate($attrs['date_of_last_v5c_issuance'] ?? null, 'Y-m-d\\TH:i'),
                'co2_emissions' => $attrs['co2_emissions'] ?? '',
                'marked_for_export' => (bool) ($attrs['marked_for_export'] ?? false),
            ];
        } else {
            $this->form = [
                'reg_no' => '',
                'make' => '',
                'model' => '',
                'year' => '',
                'vin_number' => '',
                'engine' => '',
                'color' => '',
                'fuel_type' => 'Petrol',
                'is_ebike' => false,
                'branch_id' => '',
                'wheel_plan' => '2 Wheel',
                'type_approval' => 'L3',
                'vehicle_profile_id' => '',
                'month_of_first_registration' => '',
                'date_of_last_v5c_issuance' => '',
                'co2_emissions' => '',
                'marked_for_export' => false,
            ];
        }
    }

    public function save(): void
    {
        $this->form['is_ebike'] = (bool) ($this->form['is_ebike'] ?? false);
        $this->form['marked_for_export'] = (bool) ($this->form['marked_for_export'] ?? false);

        $this->validate([
            'form.reg_no' => ['required', 'string', 'max:20'],
            'form.make' => ['required', 'string', 'max:100'],
            'form.model' => ['required', 'string', 'max:100'],
            'form.year' => ['nullable', 'string', 'max:4'],
            'form.vin_number' => ['nullable', 'string', 'max:50'],
            'form.engine' => ['nullable', 'string', 'max:50'],
            'form.color' => ['nullable', 'string', 'max:50'],
            'form.fuel_type' => ['nullable', 'string', 'max:100'],
            'form.is_ebike' => ['boolean'],
            'form.branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'form.wheel_plan' => ['nullable', 'string', 'max:50'],
            'form.type_approval' => ['nullable', 'string', 'max:50'],
            'form.vehicle_profile_id' => ['nullable', 'integer'],
            'form.month_of_first_registration' => ['nullable', 'date'],
            'form.date_of_last_v5c_issuance' => ['nullable', 'date'],
            'form.co2_emissions' => ['nullable', 'string', 'max:50'],
            'form.marked_for_export' => ['boolean'],
        ]);

        $data = [
            'reg_no' => $this->form['reg_no'],
            'make' => $this->form['make'],
            'model' => $this->form['model'],
            'year' => $this->form['year'] ?: null,
            'vin_number' => $this->form['vin_number'] ?: null,
            'engine' => $this->form['engine'] ?: null,
            'color' => $this->form['color'] ?: null,
            'fuel_type' => trim((string) ($this->form['fuel_type'] ?? '')) ?: 'Petrol',
            'is_ebike' => (bool) ($this->form['is_ebike'] ?? false),
            'branch_id' => $this->form['branch_id'] ?: null,
            'wheel_plan' => $this->form['wheel_plan'] ?: null,
            'type_approval' => $this->form['type_approval'] ?: 'L3',
            'vehicle_profile_id' => $this->form['vehicle_profile_id'] !== '' ? $this->form['vehicle_profile_id'] : null,
            'month_of_first_registration' => $this->form['month_of_first_registration'] ?: null,
            'date_of_last_v5c_issuance' => $this->form['date_of_last_v5c_issuance'] ?: null,
            'co2_emissions' => $this->form['co2_emissions'] !== '' ? $this->form['co2_emissions'] : null,
            'marked_for_export' => (bool) ($this->form['marked_for_export'] ?? false) ? 1 : 0,
        ];

        if ($this->motorbikeId) {
            Motorbike::findOrFail($this->motorbikeId)->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Motorbike updated.');
        } else {
            Motorbike::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Motorbike created.');
        }

        $this->redirect(route('flux-admin.motorbikes.index'), navigate: true);
    }

    private function formatDate(mixed $value, string $format): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return Carbon::parse($value)->format($format);
        } catch (\Throwable) {
            return '';
        }
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.form', [
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }
}
