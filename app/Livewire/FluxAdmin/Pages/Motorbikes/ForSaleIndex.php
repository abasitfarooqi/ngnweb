<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Motorcycle;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('New motorbikes for sale — Flux Admin')]
class ForSaleIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'new-motorbikes-for-sale';
    }

    protected function formModel(): string
    {
        return Motorcycle::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.make'           => ['required', 'string', 'max:255'],
            'formData.model'          => ['required', 'string', 'max:255'],
            'formData.year'           => ['nullable', 'string', 'max:4'],
            'formData.colour'         => ['nullable', 'string', 'max:255'],
            'formData.engine'         => ['nullable', 'string', 'max:255'],
            'formData.type'           => ['nullable', 'string', 'in:manual,automatic,other'],
            'formData.sale_new_price' => ['nullable', 'numeric', 'min:0'],
            'formData.description'    => ['nullable', 'string'],
            'formData.availability'   => ['nullable', 'string', 'in:for sale,sold,reserved'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['availability' => 'for sale'];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(Motorcycle::findOrFail($id));
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
        Motorcycle::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $bikes = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.for-sale-index', ['bikes' => $bikes]);
    }

    protected function baseQuery(): Builder
    {
        return Motorcycle::query()->where('availability', 'for sale')
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(fn ($q) => $q->where('make', 'like', "%{$term}%")->orWhere('model', 'like', "%{$term}%"));
            })
            ->when($this->filter('type'), fn ($q, $v) => $q->where('type', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Make' => 'make', 'Model' => 'model', 'Year' => 'year',
            'Type' => 'type', 'Engine' => 'engine', 'Colour' => 'colour', 'Sale price' => 'sale_new_price',
        ];
    }
}
