<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ContractExtraItem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Contract extra items — Flux Admin')]
class ContractExtraItemIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-finance-applications');
    }

    protected function formModel(): string { return ContractExtraItem::class; }

    protected function formRules(): array
    {
        return [
            'formData.application_id' => ['required', 'integer', 'exists:finance_applications,id'],
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.price' => ['required', 'numeric', 'min:0'],
            'formData.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['quantity' => 1];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $item = ContractExtraItem::findOrFail($id);
        $this->fillFromModel($item);
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
        ContractExtraItem::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $items = $this->baseQuery()
            ->with('application.customer:id,first_name,last_name')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.finance.contract-extra-items-index', ['items' => $items]);
    }

    protected function baseQuery(): Builder
    {
        return ContractExtraItem::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('application_id', $term));
            });
    }
}
