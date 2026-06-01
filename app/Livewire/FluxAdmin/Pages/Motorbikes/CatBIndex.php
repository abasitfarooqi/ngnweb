<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeCatB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Category B motorbikes — Flux Admin')]
class CatBIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'motorbikes-cat-b';
    }

    protected function formModel(): string { return MotorbikeCatB::class; }

    protected function formRules(): array
    {
        return [
            'formData.motorbike_id' => ['required', 'integer'],
            'formData.dop'          => ['nullable', 'date'],
            'formData.notes'        => ['nullable', 'string'],
            'formData.branch_id'    => ['nullable', 'integer'],
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
        $r = MotorbikeCatB::findOrFail($id);
        $this->fillFromModel($r);
        if (! empty($this->formData['dop'])) {
            $this->formData['dop'] = Carbon::parse($this->formData['dop'])->format('Y-m-d');
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
        MotorbikeCatB::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['motorbike:id,reg_no,make,model', 'branch:id,name'])
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $branches = \App\Models\Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.cat-b-index', compact('rows', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return MotorbikeCatB::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$term}%"));
            });
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['motorbike:id,reg_no', 'branch:id,name']); }

    protected function exportColumns(): array
    {
        return [
            'ID'               => 'id',
            'Registration'     => fn ($r) => $r->motorbike?->reg_no,
            'Date of purchase' => fn ($r) => $r->dop ? Carbon::parse($r->dop)->format('Y-m-d') : '',
            'Notes'            => 'notes',
            'Branch'           => fn ($r) => $r->branch?->name,
        ];
    }
}
