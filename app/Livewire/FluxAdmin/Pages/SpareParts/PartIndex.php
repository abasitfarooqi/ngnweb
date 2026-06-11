<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\SpPart;
use App\Models\SpStockMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts — Parts')]
class PartIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    protected array $branchIds = [
        'catford_stock' => 1,
        'tooting_stock' => 2,
        'sutton_stock' => 3,
    ];

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'spare-parts';
    }

    protected function formModel(): string { return SpPart::class; }

    protected function formRules(): array
    {
        return [
            'formData.part_number' => ['required', 'string', 'max:100'],
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.note' => ['nullable', 'string'],
            'formData.stock_status' => ['nullable', 'string', 'max:50'],
            'formData.price_gbp_inc_vat' => ['nullable', 'numeric', 'min:0'],
            'formData.global_stock' => ['nullable', 'numeric', 'min:0'],
            'formData.is_active' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_active' => true, 'stock_status' => 'in_stock'];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(SpPart::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Part saved.');
    }

    public function delete(int $id): void
    {
        SpPart::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function updateBranchStock(int $partId, string $field, mixed $value): void
    {
        if (! array_key_exists($field, $this->branchIds)) {
            abort(422, 'Invalid branch stock field.');
        }

        $targetStock = max(0, (float) $value);
        $branchId = $this->branchIds[$field];

        DB::transaction(function () use ($partId, $branchId, $targetStock): void {
            $part = SpPart::query()->lockForUpdate()->findOrFail($partId);
            $currentStock = $this->currentBranchStock($part->id, $branchId);
            $difference = $targetStock - $currentStock;

            if (abs($difference) > 0.0001) {
                SpStockMovement::query()->create([
                    'sp_part_id' => $part->id,
                    'branch_id' => $branchId,
                    'in' => $difference > 0 ? $difference : 0,
                    'out' => $difference < 0 ? abs($difference) : 0,
                    'transaction_type' => $difference > 0 ? 'Stock Adjustment' : 'Shop Sale',
                    'transaction_date' => now(),
                    'user_id' => auth()->id(),
                    'remarks' => 'Flux admin inline stock edit',
                ]);
            }

            $part->forceFill([
                'global_stock' => collect($this->branchIds)
                    ->sum(fn (int $id): float => $this->currentBranchStock($part->id, $id)),
            ])->save();
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Branch stock updated.');
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy('part_number')->paginate($this->perPage);

        return view('flux-admin.pages.spare-parts.parts-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return SpPart::query()
            ->select('sp_parts.*')
            ->addSelect([
                'catford_stock' => SpStockMovement::query()
                    ->selectRaw('COALESCE(SUM(`in`),0) - COALESCE(SUM(`out`),0)')
                    ->whereColumn('sp_part_id', 'sp_parts.id')
                    ->where('branch_id', 1),
                'tooting_stock' => SpStockMovement::query()
                    ->selectRaw('COALESCE(SUM(`in`),0) - COALESCE(SUM(`out`),0)')
                    ->whereColumn('sp_part_id', 'sp_parts.id')
                    ->where('branch_id', 2),
                'sutton_stock' => SpStockMovement::query()
                    ->selectRaw('COALESCE(SUM(`in`),0) - COALESCE(SUM(`out`),0)')
                    ->whereColumn('sp_part_id', 'sp_parts.id')
                    ->where('branch_id', 3),
            ])
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('part_number', 'like', "%{$v}%")->orWhere('name', 'like', "%{$v}%")))
            ->when($this->filter('stock_status'), fn ($q, $v) => $q->where('stock_status', $v))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'Part #' => 'part_number',
            'Name' => 'name',
            'Note' => 'note',
            'Stock status' => 'stock_status',
            'Price (inc VAT)' => 'price_gbp_inc_vat',
            'Catford stock' => 'catford_stock',
            'Tooting stock' => 'tooting_stock',
            'Sutton stock' => 'sutton_stock',
            'Global stock' => 'global_stock',
            'Last synced' => fn ($r) => $r->last_synced_at?->format('Y-m-d H:i'),
        ];
    }

    protected function currentBranchStock(int $partId, int $branchId): float
    {
        $in = (float) SpStockMovement::query()->where('sp_part_id', $partId)->where('branch_id', $branchId)->sum('in');
        $out = (float) SpStockMovement::query()->where('sp_part_id', $partId)->where('branch_id', $branchId)->sum('out');

        return $in - $out;
    }
}
