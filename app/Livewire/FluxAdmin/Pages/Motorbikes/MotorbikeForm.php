<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Motorbike;
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
                'reg_no'     => $attrs['reg_no'] ?? '',
                'make'       => $attrs['make'] ?? '',
                'model'      => $attrs['model'] ?? '',
                'year'       => $attrs['year'] ?? '',
                'vin_number' => $attrs['vin_number'] ?? '',
                'engine'     => $attrs['engine'] ?? '',
                'color'      => $attrs['color'] ?? '',
                'fuel_type'  => $attrs['fuel_type'] ?? '',
                'is_ebike'   => (bool) ($attrs['is_ebike'] ?? false),
                'branch_id'  => $attrs['branch_id'] ?? '',
            ];
        } else {
            $this->form = [
                'reg_no'     => '',
                'make'       => '',
                'model'      => '',
                'year'       => '',
                'vin_number' => '',
                'engine'     => '',
                'color'      => '',
                'fuel_type'  => '',
                'is_ebike'   => false,
                'branch_id'  => '',
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.reg_no'     => ['required', 'string', 'max:20'],
            'form.make'       => ['required', 'string', 'max:100'],
            'form.model'      => ['required', 'string', 'max:100'],
            'form.year'       => ['nullable', 'string', 'max:4'],
            'form.vin_number' => ['nullable', 'string', 'max:50'],
            'form.engine'     => ['nullable', 'string', 'max:50'],
            'form.color'      => ['nullable', 'string', 'max:50'],
            'form.fuel_type'  => ['nullable', 'string', 'in:Petrol,Diesel,Electric,Hybrid'],
            'form.is_ebike'   => ['boolean'],
            'form.branch_id'  => ['nullable', 'integer', 'exists:branches,id'],
        ]);

        $data = [
            'reg_no'     => $this->form['reg_no'],
            'make'       => $this->form['make'],
            'model'      => $this->form['model'],
            'year'       => $this->form['year'] ?: null,
            'vin_number' => $this->form['vin_number'] ?: null,
            'engine'     => $this->form['engine'] ?: null,
            'color'      => $this->form['color'] ?: null,
            'fuel_type'  => $this->form['fuel_type'] ?: null,
            'is_ebike'   => (bool) ($this->form['is_ebike'] ?? false),
            'branch_id'  => $this->form['branch_id'] ?: null,
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

    public function render()
    {
        return view('flux-admin.pages.motorbikes.form', [
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }
}
