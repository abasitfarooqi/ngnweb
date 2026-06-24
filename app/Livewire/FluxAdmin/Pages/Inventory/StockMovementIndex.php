<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Stock movements — Flux Admin')]
class StockMovementIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'stock-movements';
        $this->sortField = 'transaction_date';
    }

    public function delete(int $id): void
    {
        $movement = NgnStockMovement::query()->findOrFail($id);
        $productId = (int) $movement->product_id;
        NgnProduct::query()
            ->whereKey($productId)
            ->increment('global_stock', -1 * ((float) $movement->in - (float) $movement->out));
        $movement->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);
        $transactionTypes = [
            'stock_adjustment' => 'Stock Adjustment',
            'stock_transfer' => 'Stock Transfer',
            'stock_purchase' => 'Stock Purchase',
            'shop_sale' => 'Shop Sale',
            'online_sale' => 'Online Sale',
            'opening_stock' => 'Opening Stock',
        ];

        return view('flux-admin.pages.inventory.stock-movements-index', compact('rows', 'branches', 'transactionTypes'));
    }

    protected function baseQuery(): Builder
    {
        return NgnStockMovement::query()
            ->with(['branch:id,name', 'product:id,sku,name'])
            ->when($this->search, fn ($q, $v) => $q->where('ref_doc_no', 'like', "%{$v}%")->orWhere('remarks', 'like', "%{$v}%"))
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v))
            ->when($this->filter('transaction_type'), fn ($q, $v) => $q->where('transaction_type', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'Date' => fn ($r) => $r->transaction_date ? \Carbon\Carbon::parse($r->transaction_date)->format('Y-m-d') : '',
            'Branch ID' => 'branch_id', 'Product ID' => 'product_id',
            'In' => 'in', 'Out' => 'out', 'Type' => 'transaction_type', 'Ref doc' => 'ref_doc_no', 'Remarks' => 'remarks',
        ];
    }
}
