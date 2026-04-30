<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\MotorbikeRepairUpdate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Repair updates — Flux Admin')]
class RepairUpdateIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return MotorbikeRepairUpdate::class; }

    protected function formRules(): array
    {
        return [
            'formData.motorbike_repair_id' => ['required', 'integer'],
            'formData.job_description' => ['required', 'string'],
            'formData.price' => ['required', 'numeric', 'min:0'],
            'formData.note' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['price' => 0];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(MotorbikeRepairUpdate::findOrFail($id));
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
        MotorbikeRepairUpdate::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = MotorbikeRepairUpdate::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('job_description', 'like', "%{$v}%")->orWhere('motorbike_repair_id', $v)))
            ->when($this->filter('motorbike_repair_id'), fn ($q, $v) => $q->where('motorbike_repair_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.repair-updates-index', ['rows' => $rows]);
    }
}
