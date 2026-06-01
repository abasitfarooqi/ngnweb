<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnProduct;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Store front — Flux Admin')]
class StoreIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-ecommerce');
        $this->exportable = true;
        $this->exportFilename = 'ngn-store-products';
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    protected function formModel(): string
    {
        return NgnProduct::class;
    }

    protected function formRules(): array
    {
        return [
            'sku'          => ['nullable', 'string', 'max:100'],
            'name'         => ['required', 'string', 'max:255'],
            'brand_id'     => ['nullable', 'integer', 'exists:ngn_brands,id'],
            'category_id'  => ['nullable', 'integer', 'exists:ngn_categories,id'],
            'normal_price' => ['nullable', 'numeric', 'min:0'],
            'pos_price'    => ['nullable', 'numeric', 'min:0'],
            'global_stock' => ['nullable', 'integer', 'min:0'],
            'is_oxford'    => ['boolean'],
            'is_ecommerce' => ['boolean'],
            'description'  => ['nullable', 'string'],
            'slug'         => ['nullable', 'string', 'max:255'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_oxford' => false, 'is_ecommerce' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnProduct::findOrFail($id));
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
        NgnProduct::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.store-index', [
            'rows'       => $rows,
            'brands'     => NgnBrand::orderBy('name')->get(),
            'categories' => NgnCategory::orderBy('name')->get(),
        ]);
    }

    protected function baseQuery(): Builder
    {
        return NgnProduct::with(['brand', 'category', 'productModel', 'stockMovements.branch'])
            ->where('normal_price', '>', 0)
            ->where(function ($q) {
                $q->where('is_oxford', 1)->orWhere('is_ecommerce', 1);
            })
            ->when($this->search, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('name', 'like', "%{$v}%")->orWhere('sku', 'like', "%{$v}%");
                });
            })
            ->when($this->filter('is_oxford') !== '', fn ($q) => $q->where('is_oxford', $this->filter('is_oxford')))
            ->when($this->filter('is_ecommerce') !== '', fn ($q) => $q->where('is_ecommerce', $this->filter('is_ecommerce')));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'SKU'          => 'sku',
            'Name'         => 'name',
            'Brand'        => fn ($r) => $r->brand?->name,
            'Category'     => fn ($r) => $r->category?->name,
            'Normal price' => 'normal_price',
            'POS price'    => 'pos_price',
            'Global stock' => 'global_stock',
            'Oxford'       => fn ($r) => $r->is_oxford ? 'Yes' : 'No',
            'Shop'         => fn ($r) => $r->is_ecommerce ? 'Yes' : 'No',
        ];
    }
}
