<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SpAssembly;
use App\Models\SpFitment;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts — Assemblies')]
class AssemblyIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return SpAssembly::class; }

    protected function formRules(): array
    {
        return [
            'formData.fitment_id' => ['required', 'integer', 'exists:sp_fitments,id'],
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.slug' => ['nullable', 'string', 'max:255'],
            'formData.external_id' => ['nullable', 'string', 'max:255'],
            'formData.image_url' => ['nullable', 'string', 'max:1024'],
            'formData.diagram_url' => ['nullable', 'string', 'max:1024'],
            'formData.sort_order' => ['nullable', 'integer', 'min:0'],
            'formData.is_active' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_active' => true, 'sort_order' => 0];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(SpAssembly::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        if (empty($this->formData['slug']) && ! empty($this->formData['name'])) {
            $this->formData['slug'] = Str::slug($this->formData['name']);
        }
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Assembly saved.');
    }

    public function delete(int $id): void
    {
        SpAssembly::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = SpAssembly::query()
            ->with('fitment.model:id,name')
            ->when($this->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $fitments = SpFitment::query()->with('model:id,name')->orderByDesc('id')->limit(500)->get();

        return view('flux-admin.pages.spare-parts.assemblies-index', compact('rows', 'fitments'));
    }
}
