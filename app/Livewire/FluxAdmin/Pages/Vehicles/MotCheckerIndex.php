<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotChecker;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT checker subscribers — Flux Admin')]
class MotCheckerIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'mot-checker-subscribers';
        $this->sortField = 'mot_due_date';
        $this->sortDirection = 'asc';
    }

    protected function formModel(): string { return MotChecker::class; }

    protected function formRules(): array
    {
        return [
            'formData.vehicle_registration' => ['required', 'string', 'max:20'],
            'formData.mot_due_date' => ['required', 'date'],
            'formData.email' => ['required', 'email', 'max:255'],
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
        $this->fillFromModel(MotChecker::findOrFail($id));
        if (! empty($this->formData['mot_due_date'])) {
            $this->formData['mot_due_date'] = \Carbon\Carbon::parse($this->formData['mot_due_date'])->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Subscriber saved.');
    }

    public function delete(int $id): void
    {
        MotChecker::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.mot-checker-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return MotChecker::query()
            ->when($this->search, fn ($q, $v) => $q->where('vehicle_registration', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%"))
            ->when($this->filter('horizon') === 'upcoming', fn ($q) => $q->whereDate('mot_due_date', '>=', now()))
            ->when($this->filter('horizon') === 'overdue', fn ($q) => $q->whereDate('mot_due_date', '<', now()));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'VRM' => 'vehicle_registration',
            'MOT due' => fn ($r) => $r->mot_due_date ? \Carbon\Carbon::parse($r->mot_due_date)->format('Y-m-d') : '',
            'Email' => 'email',
        ];
    }
}
