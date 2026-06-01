<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\CompanyVehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Company vehicles — Flux Admin')]
class CompanyVehicleIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    protected function formModel(): string
    {
        return CompanyVehicle::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.custodian'   => ['required', 'string', 'max:255'],
            'formData.motorbike_id' => ['required', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(CompanyVehicle::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        CompanyVehicle::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = CompanyVehicle::query()
            ->with('motorbike:id,reg_no,make,model')
            ->when($this->search, fn ($q, $v) => $q->where('custodian', 'like', "%{$v}%")->orWhereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%")))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.company-vehicles-index', ['rows' => $rows]);
    }
}
