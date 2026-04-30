<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\SpPart;
use Illuminate\Database\Eloquent\Builder;
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

    public function render()
    {
        $rows = $this->baseQuery()->orderBy('part_number')->paginate($this->perPage);

        return view('flux-admin.pages.spare-parts.parts-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return SpPart::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('part_number', 'like', "%{$v}%")->orWhere('name', 'like', "%{$v}%")))
            ->when($this->filter('stock_status'), fn ($q, $v) => $q->where('stock_status', $v))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return ['Part #' => 'part_number', 'Name' => 'name', 'Note' => 'note', 'Stock status' => 'stock_status', 'Price (inc VAT)' => 'price_gbp_inc_vat', 'Global stock' => 'global_stock', 'Last synced' => fn ($r) => $r->last_synced_at?->format('Y-m-d H:i')];
    }
}
