<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\SpStockMovement;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts — Stock movements')]
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
        $this->exportFilename = 'sp-stock-movements';
        $this->sortField = 'transaction_date';
    }

    public function delete(int $id): void
    {
        SpStockMovement::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with('part:id,part_number,name')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.spare-parts.stock-movements-index', compact('rows', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return SpStockMovement::query()
            ->when($this->search, fn ($q, $v) => $q->where('ref_doc_no', 'like', "%{$v}%")->orWhere('remarks', 'like', "%{$v}%"))
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v))
            ->when($this->filter('transaction_type'), fn ($q, $v) => $q->where('transaction_type', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with('part'); }

    protected function exportColumns(): array
    {
        return [
            'Date' => fn ($r) => $r->transaction_date ? \Carbon\Carbon::parse($r->transaction_date)->format('Y-m-d') : '',
            'Part #' => fn ($r) => $r->part?->part_number, 'Part name' => fn ($r) => $r->part?->name,
            'Branch' => 'branch_id', 'In' => 'in', 'Out' => 'out', 'Type' => 'transaction_type', 'Ref' => 'ref_doc_no',
        ];
    }
}
