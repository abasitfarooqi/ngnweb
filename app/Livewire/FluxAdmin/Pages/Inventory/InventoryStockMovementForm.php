<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Inventory stock movement — Flux Admin')]
class InventoryStockMovementForm extends Component
{
    use WithAuthorization;

    public ?NgnStockMovement $ngnStockMovement = null;

    public array $form = [];

    protected array $transactionTypes = [
        'stock_adjustment' => 'Stock Adjustment',
        'stock_transfer' => 'Stock Transfer',
        'stock_purchase' => 'Stock Purchase',
        'shop_sale' => 'Shop Sale',
        'online_sale' => 'Online Sale',
        'opening_stock' => 'Opening Stock',
    ];

    public function mount(?NgnStockMovement $ngnStockMovement = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->ngnStockMovement = $ngnStockMovement;

        if ($ngnStockMovement && $ngnStockMovement->exists) {
            $this->form = $ngnStockMovement->getAttributes();
            if (! empty($this->form['transaction_date'])) {
                $this->form['transaction_date'] = \Carbon\Carbon::parse($this->form['transaction_date'])->format('Y-m-d');
            }
        } else {
            $this->form = [
                'transaction_date' => now()->toDateString(),
                'transaction_type' => 'stock_purchase',
                'in' => 0,
                'out' => 0,
                'user_id' => auth()->id(),
            ];
        }
    }

    public function save(): void
    {
        $rules = [
            'form.branch_id' => [$this->isNewTransfer() ? 'nullable' : 'required', 'integer', 'exists:branches,id'],
            'form.product_id' => ['required', 'integer', 'exists:ngn_products,id'],
            'form.transaction_date' => ['required', 'date'],
            'form.transaction_type' => ['required', 'in:stock_transfer,stock_purchase,shop_sale,online_sale,stock_adjustment,opening_stock'],
            'form.in' => ['nullable', 'numeric', 'min:0'],
            'form.out' => ['nullable', 'numeric', 'min:0'],
            'form.ref_doc_no' => ['nullable', 'string', 'max:120'],
            'form.remarks' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->isNewTransfer()) {
            $rules['form.from_branch_id'] = ['required', 'integer', 'exists:branches,id', 'different:form.to_branch_id'];
            $rules['form.to_branch_id'] = ['required', 'integer', 'exists:branches,id'];
            $rules['form.transfer_qty'] = ['required', 'numeric', 'min:1'];
        }

        $this->validate($rules);

        if ($this->isNewTransfer()) {
            $this->saveTransfer();
        } else {
            $original = $this->ngnStockMovement && $this->ngnStockMovement->exists
                ? $this->ngnStockMovement
                : null;

            $payload = collect($this->form)->only([
                'branch_id', 'product_id', 'transaction_date', 'transaction_type',
                'in', 'out', 'ref_doc_no', 'remarks',
            ])->all();
            $payload['user_id'] = $this->form['user_id'] ?? auth()->id();

            if ($original === null) {
                $payload['ref_doc_no'] = ($payload['ref_doc_no'] ?? null) ?: $this->newReferenceNumber();
            }

            if ($original) {
                $original->update($payload);
                $movement = $original->fresh();
            } else {
                $movement = NgnStockMovement::create($payload);
            }

            $this->applyMovementStockDelta($movement, $original);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Movement saved.');
        $this->redirect(route('flux-admin.inventory-stock-movements.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);
        $products = NgnProduct::query()->orderBy('name')->limit(500)->get(['id', 'sku', 'name']);
        $transactionTypes = $this->transactionTypes;

        return view('flux-admin.pages.inventory.inventory-stock-movement-form', compact('branches', 'products', 'transactionTypes'));
    }

    private function saveTransfer(): void
    {
        $productId = (int) $this->form['product_id'];
        $qty = (float) $this->form['transfer_qty'];
        $refDocNo = trim((string) ($this->form['ref_doc_no'] ?? '')) ?: $this->newReferenceNumber();
        $base = [
            'product_id' => $productId,
            'transaction_date' => $this->form['transaction_date'],
            'transaction_type' => 'stock_transfer',
            'user_id' => $this->form['user_id'] ?? auth()->id(),
            'ref_doc_no' => $refDocNo,
            'remarks' => $this->form['remarks'] ?? null,
        ];

        DB::transaction(function () use ($base, $qty, $productId): void {
            NgnStockMovement::query()->create($base + [
                'branch_id' => (int) $this->form['from_branch_id'],
                'in' => 0,
                'out' => $qty,
            ]);

            NgnStockMovement::query()->create($base + [
                'branch_id' => (int) $this->form['to_branch_id'],
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
        return 'REF-'.now()->format('YmdHis').'-'.Str::random(5);
    }

    private function isTransfer(): bool
    {
        return ($this->form['transaction_type'] ?? null) === 'stock_transfer';
    }

    private function isNewTransfer(): bool
    {
        return $this->isTransfer() && ! ($this->ngnStockMovement && $this->ngnStockMovement->exists);
    }
}
