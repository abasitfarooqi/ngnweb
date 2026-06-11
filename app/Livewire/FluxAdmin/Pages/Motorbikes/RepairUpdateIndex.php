<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\MotorbikeRepairUpdate;
use App\Models\MotorbikeRepairServicesList;
use Illuminate\Database\Eloquent\Model;
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

    public function mount(?MotorbikeRepairUpdate $motorbikeRepairUpdate = null): void
    {
        $this->authorizeModule('see-menu-commons');

        if ($motorbikeRepairUpdate && $motorbikeRepairUpdate->exists) {
            $this->openEdit($motorbikeRepairUpdate->id);
        } elseif (request()->routeIs('flux-admin.backpack.motorbike-repair-update.create')) {
            $this->openCreate();
        }
    }

    protected function formModel(): string { return MotorbikeRepairUpdate::class; }

    protected function formRules(): array
    {
        return [
            'formData.motorbike_repair_id' => ['required', 'integer'],
            'formData.job_description' => ['required', 'string'],
            'formData.price' => ['required', 'numeric', 'min:0'],
            'formData.note' => ['nullable', 'string'],
            'formData.services' => ['array'],
            'formData.services.*' => ['integer', 'exists:motorbike_repair_services_lists,id'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['price' => 0, 'services' => []];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $update = MotorbikeRepairUpdate::with('services')->findOrFail($id);
        $this->fillFromModel($update);
        $this->formData['services'] = $update->services->pluck('id')->map(fn ($id) => (string) $id)->all();
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
        $update = MotorbikeRepairUpdate::findOrFail($id);
        $update->services()->detach();
        $update->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = MotorbikeRepairUpdate::query()
            ->with('services:id,name')
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('job_description', 'like', "%{$v}%")->orWhere('motorbike_repair_id', $v)))
            ->when($this->filter('motorbike_repair_id'), fn ($q, $v) => $q->where('motorbike_repair_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $services = MotorbikeRepairServicesList::query()->orderBy('name')->get(['id', 'name', 'price']);

        return view('flux-admin.pages.motorbikes.repair-updates-index', ['rows' => $rows, 'services' => $services]);
    }

    protected function afterSave(Model $model): void
    {
        if (! $model instanceof MotorbikeRepairUpdate) {
            return;
        }

        $ids = collect($this->formData['services'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $model->services()->sync($ids);
    }
}
