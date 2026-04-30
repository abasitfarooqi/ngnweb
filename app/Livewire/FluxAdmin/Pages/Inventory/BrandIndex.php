<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnBrand;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Brands — Flux Admin')]
class BrandIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return NgnBrand::class; }

    protected function formRules(): array
    {
        return [
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.slug' => ['nullable', 'string', 'max:255'],
            'formData.description' => ['nullable', 'string'],
            'formData.image_url' => ['nullable', 'string', 'max:1024'],
            'formData.sort_order' => ['nullable', 'integer', 'min:0'],
            'formData.is_active' => ['boolean'],
            'formData.is_ecommerce' => ['boolean'],
            'formData.meta_title' => ['nullable', 'string', 'max:255'],
            'formData.meta_description' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_active' => true, 'is_ecommerce' => false, 'sort_order' => 0];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnBrand::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        if (empty($this->formData['slug']) && ! empty($this->formData['name'])) {
            $this->formData['slug'] = Str::slug($this->formData['name']);
        }
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Brand saved.');
    }

    public function delete(int $id): void
    {
        NgnBrand::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = NgnBrand::query()
            ->when($this->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'))
            ->when($this->filter('is_ecommerce') !== '', fn ($q) => $q->where('is_ecommerce', $this->filter('is_ecommerce') === '1'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('flux-admin.pages.inventory.brands-index', ['rows' => $rows]);
    }
}
