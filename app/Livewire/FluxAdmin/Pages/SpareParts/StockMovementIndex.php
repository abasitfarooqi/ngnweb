<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\SpPart;
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
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'sp-stock-movements';
        $this->sortField = 'transaction_date';
    }

    protected function formModel(): string { return SpStockMovement::class; }

    protected function formRules(): array
    {
        return [
            'formData.branch_id' => ['required', 'integer', 'exists:branches,id'],
            'formData.sp_part_id' => ['required', 'integer', 'exists:sp_parts,id'],
            'formData.transaction_date' => ['required', 'date'],
            'formData.transaction_type' => ['required', 'string', 'max:50'],
            'formData.in' => ['nullable', 'numeric', 'min:0'],
            'formData.out' => ['nullable', 'numeric', 'min:0'],
            'formData.ref_doc_no' => ['nullable', 'string', 'max:120'],
            'formData.remarks' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'transaction_date' => now()->toDateString(),
            'transaction_type' => 'purchase',
            'in' => 0,
            'out' => 0,
            'user_id' => auth()->id(),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(SpStockMovement::findOrFail($id));
        if (! empty($this->formData['transaction_date'])) {
            $this->formData['transaction_date'] = \Carbon\Carbon::parse($this->formData['transaction_date'])->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->formData['user_id'] = $this->formData['user_id'] ?? auth()->id();
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Movement saved.');
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
        $parts = SpPart::query()->orderBy('part_number')->limit(500)->get(['id', 'part_number', 'name']);

        return view('flux-admin.pages.spare-parts.stock-movements-index', compact('rows', 'branches', 'parts'));
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
