<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Motorbike;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('E-bike manager — Flux Admin')]
class EbikeIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    protected function formModel(): string { return Motorbike::class; }

    protected function formRules(): array
    {
        return [
            'formData.reg_no'     => ['nullable', 'string', 'max:20'],
            'formData.make'       => ['nullable', 'string', 'max:120'],
            'formData.model'      => ['nullable', 'string', 'max:120'],
            'formData.year'       => ['nullable', 'integer'],
            'formData.color'      => ['nullable', 'string', 'max:80'],
            'formData.vin_number' => ['nullable', 'string', 'max:80'],
            'formData.engine'     => ['nullable', 'string', 'max:80'],
            'formData.branch_id'  => ['nullable', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_ebike' => true];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $b = Motorbike::findOrFail($id);
        $this->fillFromModel($b);
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
        Motorbike::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $bikes = $this->baseQuery()
            ->with([
                'registrations'   => fn ($q) => $q->orderByDesc('start_date'),
                'rentingPricings' => fn ($q) => $q->where('iscurrent', true)->orderByDesc('update_date'),
            ])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $branches = \App\Models\Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.ebikes-index', compact('bikes', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return Motorbike::query()->where('is_ebike', true)
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('make', 'like', "%{$term}%")
                        ->orWhere('model', 'like', "%{$term}%")
                        ->orWhere('vin_number', 'like', "%{$term}%")
                        ->orWhere('reg_no', 'like', "%{$term}%");
                });
            })
            ->when($this->filter('make'), fn ($q, $v) => $q->where('make', 'like', "%{$v}%"));
    }
}
