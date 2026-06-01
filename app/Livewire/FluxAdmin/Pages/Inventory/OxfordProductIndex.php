<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\OxfordProducts;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Oxford products — Flux Admin')]
class OxfordProductIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'oxford-products';
    }

    protected function formModel(): string { return OxfordProducts::class; }

    protected function formRules(): array
    {
        return [
            'formData.sku'          => ['required', 'string', 'max:100'],
            'formData.description'  => ['nullable', 'string', 'max:500'],
            'formData.ean'          => ['nullable', 'string', 'max:50'],
            'formData.brand'        => ['nullable', 'string', 'max:120'],
            'formData.supplier'     => ['nullable', 'string', 'max:120'],
            'formData.supplier_code' => ['nullable', 'string', 'max:100'],
            'formData.rrp_inc_vat'  => ['nullable', 'numeric', 'min:0'],
            'formData.rrp_less_vat' => ['nullable', 'numeric', 'min:0'],
            'formData.cost_price'   => ['nullable', 'numeric', 'min:0'],
            'formData.stock'        => ['nullable', 'integer'],
            'formData.catford_stock' => ['nullable', 'integer'],
            'formData.colour'       => ['nullable', 'string', 'max:100'],
            'formData.variation'    => ['nullable', 'string', 'max:100'],
            'formData.vatable'      => ['nullable', 'boolean'],
            'formData.obsolete'     => ['nullable', 'boolean'],
            'formData.dead'         => ['nullable', 'boolean'],
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
        $this->fillFromModel(OxfordProducts::findOrFail($id));
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
        OxfordProducts::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy('sku')->paginate($this->perPage);

        return view('flux-admin.pages.inventory.oxford-products-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return OxfordProducts::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('sku', 'like', "%{$v}%")->orWhere('ean', 'like', "%{$v}%")->orWhere('description', 'like', "%{$v}%")))
            ->when($this->filter('obsolete') !== '', fn ($q) => $q->where('obsolete', $this->filter('obsolete') === '1'))
            ->when($this->filter('dead') !== '', fn ($q) => $q->where('dead', $this->filter('dead') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return ['SKU' => 'sku', 'EAN' => 'ean', 'Description' => 'description', 'RRP inc VAT' => 'rrp_inc_vat', 'Cost' => 'cost_price', 'Stock' => 'stock', 'Catford stock' => 'catford_stock', 'Brand' => 'brand', 'Supplier' => 'supplier'];
    }
}
