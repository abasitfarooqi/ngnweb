<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Stock movements — Flux Admin')]
class StockMovementIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    protected array $transactionTypes = [
        'stock_adjustment' => 'Stock Adjustment',
        'stock_transfer' => 'Stock Transfer',
        'stock_purchase' => 'Stock Purchase',
        'shop_sale' => 'Shop Sale',
        'online_sale' => 'Online Sale',
        'opening_stock' => 'Opening Stock',
    ];

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'stock-movements';
        $this->sortField = 'transaction_date';
    }

    protected function formModel(): string { return NgnStockMovement::class; }

    protected function formRules(): array
    {
        $rules = [
            'formData.branch_id' => [$this->isNewTransfer() ? 'nullable' : 'required', 'integer', 'exists:branches,id'],
            'formData.product_id' => ['required', 'integer', 'exists:ngn_products,id'],
            'formData.transaction_date' => ['required', 'date'],
            'formData.transaction_type' => ['required', 'in:stock_transfer,stock_purchase,shop_sale,online_sale,stock_adjustment,opening_stock'],
            'formData.in' => ['nullable', 'numeric', 'min:0'],
            'formData.out' => ['nullable', 'numeric', 'min:0'],
            'formData.ref_doc_no' => ['nullable', 'string', 'max:120'],
            'formData.remarks' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->isNewTransfer()) {
            $rules['formData.from_branch_id'] = ['required', 'integer', 'exists:branches,id', 'different:formData.to_branch_id'];
            $rules['formData.to_branch_id'] = ['required', 'integer', 'exists:branches,id'];
            $rules['formData.transfer_qty'] = ['required', 'numeric', 'min:1'];
        }

        return $rules;
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'transaction_date' => now()->toDateString(),
            'transaction_type' => 'stock_purchase',
            'in' => 0,
            'out' => 0,
            'user_id' => auth()->id(),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnStockMovement::findOrFail($id));
        if (! empty($this->formData['transaction_date'])) {
            $this->formData['transaction_date'] = \Carbon\Carbon::parse($this->formData['transaction_date'])->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        if ($this->isTransfer() && $this->recordId === null) {
            $this->saveTransfer();
        } else {
            $original = $this->recordId
                ? NgnStockMovement::query()->findOrFail($this->recordId)
                : null;
            $this->formData['user_id'] = $this->formData['user_id'] ?? auth()->id();
            $movement = $this->save();
            $this->applyMovementStockDelta($movement, $original);
        }

        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Movement saved.');
    }

    public function delete(int $id): void
    {
        $movement = NgnStockMovement::query()->findOrFail($id);
        $productId = (int) $movement->product_id;
        $this->applyGlobalStockDelta($productId, -1 * (float) $movement->in, -1 * (float) $movement->out);
        $movement->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);
        $products = NgnProduct::query()->orderBy('name')->limit(500)->get(['id', 'sku', 'name']);

        $transactionTypes = $this->transactionTypes;

        return view('flux-admin.pages.inventory.stock-movements-index', compact('rows', 'branches', 'products', 'transactionTypes'));
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

    protected function beforeSave(array $attributes): array
    {
        if ($this->recordId === null) {
            $attributes['ref_doc_no'] = ($attributes['ref_doc_no'] ?? null) ?: $this->newReferenceNumber();
        }

        return $attributes;
    }

    private function saveTransfer(): void
    {
        $validator = validator(
            ['formData' => $this->formData],
            ['formData' => 'array'] + $this->ruleKeysOf($this->formRules()),
        );

        $validator->validate();

        $productId = (int) $this->formData['product_id'];
        $qty = (float) $this->formData['transfer_qty'];
        $refDocNo = trim((string) ($this->formData['ref_doc_no'] ?? '')) ?: $this->newReferenceNumber();
        $base = [
            'product_id' => $productId,
            'transaction_date' => $this->formData['transaction_date'],
            'transaction_type' => 'stock_transfer',
            'user_id' => $this->formData['user_id'] ?? auth()->id(),
            'ref_doc_no' => $refDocNo,
            'remarks' => $this->formData['remarks'] ?? null,
        ];

        DB::transaction(function () use ($base, $qty, $productId): void {
            NgnStockMovement::query()->create($base + [
                'branch_id' => (int) $this->formData['from_branch_id'],
                'in' => 0,
                'out' => $qty,
            ]);

            NgnStockMovement::query()->create($base + [
                'branch_id' => (int) $this->formData['to_branch_id'],
                'in' => $qty,
                'out' => 0,
            ]);

            $this->applyGlobalStockDelta($productId, 0, $qty);
            $this->applyGlobalStockDelta($productId, $qty, 0);
        });
    }

    private function applyGlobalStockDelta(int $productId, float $in, float $out): void
    {
        NgnProduct::query()
            ->whereKey($productId)
            ->increment('global_stock', $in - $out);
    }

    private function applyMovementStockDelta(NgnStockMovement $movement, ?NgnStockMovement $original): void
    {
        if ($original && (int) $original->product_id !== (int) $movement->product_id) {
            $this->applyGlobalStockDelta((int) $original->product_id, -1 * (float) $original->in, -1 * (float) $original->out);
            $this->applyGlobalStockDelta((int) $movement->product_id, (float) $movement->in, (float) $movement->out);

            return;
        }

        $inChange = (float) $movement->in - (float) ($original?->in ?? 0);
        $outChange = (float) $movement->out - (float) ($original?->out ?? 0);

        $this->applyGlobalStockDelta((int) $movement->product_id, $inChange, $outChange);
    }

    private function newReferenceNumber(): string
    {
        return 'REF-' . now()->format('YmdHis') . '-' . Str::random(5);
    }

    private function isTransfer(): bool
    {
        return ($this->formData['transaction_type'] ?? null) === 'stock_transfer';
    }

    private function isNewTransfer(): bool
    {
        return $this->isTransfer() && $this->recordId === null;
    }
}
