<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Exports\NgnPOSProductsExport;
use App\Imports\StockHandler;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnModel;
use App\Models\NgnProduct;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('flux-admin.layouts.app')]
#[Title('Products — Flux Admin')]
class ProductIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithFileUploads, WithPagination;

    public bool $showForm = false;

    public bool $showImportModal = false;

    public bool $importUpdateZero = false;

    public $importFile;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'products';
    }

    protected function formModel(): string { return NgnProduct::class; }

    protected function formRules(): array
    {
        return [
            'formData.sku'              => ['nullable', 'string', 'max:100'],
            'formData.ean'              => ['nullable', 'string', 'max:50'],
            'formData.name'             => ['required', 'string', 'max:255'],
            'formData.variation'        => ['nullable', 'string', 'max:255'],
            'formData.description'      => ['nullable', 'string'],
            'formData.colour'           => ['nullable', 'string', 'max:100'],
            'formData.brand_id'         => ['nullable', 'integer'],
            'formData.category_id'      => ['nullable', 'integer'],
            'formData.model_id'         => ['nullable', 'integer'],
            'formData.normal_price'     => ['nullable', 'numeric', 'min:0'],
            'formData.pos_price'        => ['nullable', 'numeric', 'min:0'],
            'formData.pos_vat'          => ['nullable', 'numeric', 'min:0'],
            'formData.global_stock'     => ['nullable', 'integer'],
            'formData.vatable'          => ['nullable', 'boolean'],
            'formData.is_oxford'        => ['nullable', 'boolean'],
            'formData.dead'             => ['nullable', 'boolean'],
            'formData.is_ecommerce'     => ['nullable', 'boolean'],
            'formData.slug'             => ['nullable', 'string', 'max:255'],
            'formData.meta_title'       => ['nullable', 'string', 'max:255'],
            'formData.meta_description' => ['nullable', 'string', 'max:500'],
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

    public function exportForPos()
    {
        return Excel::download(new NgnPOSProductsExport, 'ngn_pos_products_'.date('Y-m-d_H-i-s').'.xlsx');
    }

    public function importStock(): void
    {
        $this->validate(['importFile' => ['required', 'file', 'mimes:xlsx,xls']]);

        Excel::import(new StockHandler($this->importUpdateZero), $this->importFile->getRealPath());

        $this->importFile = null;
        $this->showImportModal = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Stock imported successfully.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['brand:id,name', 'category:id,name', 'model:id,name'])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $brands = NgnBrand::query()->orderBy('name')->get(['id', 'name']);
        $categories = NgnCategory::query()->orderBy('name')->get(['id', 'name']);
        $models = NgnModel::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.inventory.products-index', compact('rows', 'brands', 'categories', 'models'));
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
