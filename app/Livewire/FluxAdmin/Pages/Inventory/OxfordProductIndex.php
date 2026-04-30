<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
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
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'oxford-products';
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
