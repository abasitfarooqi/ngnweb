<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NgnProduct;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Products — Flux Admin')]
class ProductIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'products';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['brand:id,name', 'category:id,name', 'model:id,name'])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $brands = \App\Models\NgnBrand::query()->orderBy('name')->get(['id', 'name']);
        $categories = \App\Models\NgnCategory::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.inventory.products-index', compact('rows', 'brands', 'categories'));
    }

    protected function baseQuery(): Builder
    {
        return NgnProduct::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('name', 'like', "%{$v}%")->orWhere('sku', 'like', "%{$v}%")->orWhere('ean', 'like', "%{$v}%")))
            ->when($this->filter('brand_id'), fn ($q, $v) => $q->where('brand_id', $v))
            ->when($this->filter('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($this->filter('is_ecommerce') !== '', fn ($q) => $q->where('is_ecommerce', $this->filter('is_ecommerce') === '1'))
            ->when($this->filter('dead') !== '', fn ($q) => $q->where('dead', $this->filter('dead') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['brand', 'category', 'model']); }

    protected function exportColumns(): array
    {
        return [
            'SKU' => 'sku', 'EAN' => 'ean', 'Name' => 'name', 'Variation' => 'variation', 'Colour' => 'colour',
            'Brand' => fn ($r) => $r->brand?->name, 'Category' => fn ($r) => $r->category?->name, 'Model' => fn ($r) => $r->model?->name,
            'Normal price' => 'normal_price', 'POS price' => 'pos_price', 'VAT' => 'pos_vat',
            'Global stock' => 'global_stock', 'Vatable' => fn ($r) => $r->vatable ? 'Yes' : 'No',
            'Oxford' => fn ($r) => $r->is_oxford ? 'Yes' : 'No', 'Dead' => fn ($r) => $r->dead ? 'Yes' : 'No',
            'Shop' => fn ($r) => $r->is_ecommerce ? 'Yes' : 'No',
        ];
    }
}
