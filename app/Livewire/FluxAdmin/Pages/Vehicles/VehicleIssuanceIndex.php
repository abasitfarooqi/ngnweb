<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\VehicleIssuance;
use App\Support\FluxAdminFormPayload;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle issuances — Flux Admin')]
class VehicleIssuanceIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-renting-page');
    }

    protected function formModel(): string
    {
        return VehicleIssuance::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.issue_date'   => ['required', 'date'],
            'formData.user_id'      => ['required', 'integer'],
            'formData.branch_id'    => ['nullable', 'integer'],
            'formData.motorbike_id' => ['required', 'integer'],
            'formData.customer_id'  => ['nullable', 'integer'],
            'formData.notes'        => ['nullable', 'string'],
            'formData.is_returned'  => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'issue_date'  => now()->toDateString(),
            'user_id'     => FluxAdminFormPayload::adminUserId(),
            'is_returned' => false,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = VehicleIssuance::findOrFail($id);
        $this->fillFromModel($record);
        if (!empty($this->formData['issue_date'])) {
            $this->formData['issue_date'] = \Carbon\Carbon::parse($this->formData['issue_date'])->format('Y-m-d');
        }
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
        VehicleIssuance::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = VehicleIssuance::query()
            ->with(['customer:id,first_name,last_name', 'motorbike:id,reg_no,make,model', 'branch:id,name', 'user:id,first_name'])
            ->when($this->search, fn ($q, $v) => $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%"))->orWhereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->when($this->filter('is_returned') !== '', fn ($q) => $q->where('is_returned', $this->filter('is_returned') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.vehicle-issuances-index', ['rows' => $rows]);
    }
}
