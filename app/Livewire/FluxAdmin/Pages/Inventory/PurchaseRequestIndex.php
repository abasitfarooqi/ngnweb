<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Purchase requests — Flux Admin')]
class PurchaseRequestIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); $this->sortField = 'date'; }

    protected function formModel(): string { return PurchaseRequest::class; }

    protected function formRules(): array
    {
        return [
            'formData.date'      => ['required', 'date'],
            'formData.note'      => ['nullable', 'string', 'max:1000'],
            'formData.is_posted' => ['nullable', 'boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['date' => now()->toDateString(), 'is_posted' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(PurchaseRequest::findOrFail($id));
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
        PurchaseRequest::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = PurchaseRequest::query()
            ->withCount('items')
            ->when($this->search, fn ($q, $v) => $q->where('note', 'like', "%{$v}%"))
            ->when($this->filter('is_posted') !== '', fn ($q) => $q->where('is_posted', $this->filter('is_posted') === '1'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.inventory.purchase-requests-index', ['rows' => $rows]);
    }
}
