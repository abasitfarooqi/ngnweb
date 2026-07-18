<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\RecoveredMotorbike;
use App\Support\FluxAdminFormPayload;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Recovered motorbikes — Flux Admin')]
class RecoveredIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'case_date';
    }

    protected function formModel(): string
    {
        return RecoveredMotorbike::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.case_date'    => ['required', 'date'],
            'formData.motorbike_id' => ['required', 'integer'],
            'formData.branch_id'    => ['nullable', 'integer'],
            'formData.notes'        => ['nullable', 'string'],
            'formData.returned_date' => ['nullable', 'date'],
            'formData.user_id'      => ['nullable', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'case_date' => now()->toDateString(),
            'user_id'   => FluxAdminFormPayload::adminUserId(),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = RecoveredMotorbike::findOrFail($id);
        $this->fillFromModel($record);
        if (!empty($this->formData['case_date'])) {
            $this->formData['case_date'] = \Carbon\Carbon::parse($this->formData['case_date'])->format('Y-m-d');
        }
        if (!empty($this->formData['returned_date'])) {
            $this->formData['returned_date'] = \Carbon\Carbon::parse($this->formData['returned_date'])->format('Y-m-d');
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
        RecoveredMotorbike::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = RecoveredMotorbike::query()
            ->with(['motorbike:id,reg_no,make,model', 'branch:id,name'])
            ->when($this->search, fn ($q, $v) => $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%")))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.recovered-index', ['rows' => $rows]);
    }
}
