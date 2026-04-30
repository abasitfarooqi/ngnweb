<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SpAssembly;
use App\Models\SpAssemblyPart;
use App\Models\SpPart;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts — Assembly parts')]
class AssemblyPartIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return SpAssemblyPart::class; }

    protected function formRules(): array
    {
        return [
            'formData.assembly_id' => ['required', 'integer', 'exists:sp_assemblies,id'],
            'formData.part_id' => ['required', 'integer', 'exists:sp_parts,id'],
            'formData.qty_used' => ['required', 'integer', 'min:1'],
            'formData.sort_order' => ['nullable', 'integer', 'min:0'],
            'formData.note_override' => ['nullable', 'string'],
            'formData.price_override' => ['nullable', 'numeric', 'min:0'],
            'formData.stock_override' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['qty_used' => 1, 'sort_order' => 0];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(SpAssemblyPart::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        foreach (['price_override', 'stock_override'] as $k) {
            if (isset($this->formData[$k]) && $this->formData[$k] === '') { $this->formData[$k] = null; }
        }
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        SpAssemblyPart::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = SpAssemblyPart::query()
            ->with(['assembly:id,name', 'part:id,part_number,name'])
            ->when($this->search, fn ($q, $v) => $q->whereHas('part', fn ($q) => $q->where('part_number', 'like', "%{$v}%")->orWhere('name', 'like', "%{$v}%")))
            ->when($this->filter('assembly_id'), fn ($q, $v) => $q->where('assembly_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $assemblies = SpAssembly::query()->orderByDesc('id')->limit(500)->get(['id', 'name']);
        $parts = SpPart::query()->orderBy('part_number')->limit(1000)->get(['id', 'part_number', 'name']);

        return view('flux-admin.pages.spare-parts.assembly-parts-index', compact('rows', 'assemblies', 'parts'));
    }
}
