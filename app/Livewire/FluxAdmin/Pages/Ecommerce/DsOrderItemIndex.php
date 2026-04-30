<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\DsOrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('DS order items — Flux Admin')]
class DsOrderItemIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return DsOrderItem::class; }

    protected function formRules(): array
    {
        return [
            'formData.ds_order_id' => ['required', 'integer'],
            'formData.vrm' => ['nullable', 'string', 'max:20'],
            'formData.pickup_address' => ['required', 'string', 'max:500'],
            'formData.pickup_postcode' => ['required', 'string', 'max:20'],
            'formData.dropoff_address' => ['required', 'string', 'max:500'],
            'formData.dropoff_postcode' => ['required', 'string', 'max:20'],
            'formData.pickup_lat' => ['nullable', 'numeric'],
            'formData.pickup_lon' => ['nullable', 'numeric'],
            'formData.dropoff_lat' => ['nullable', 'numeric'],
            'formData.dropoff_lon' => ['nullable', 'numeric'],
            'formData.moveable' => ['nullable', 'boolean'],
            'formData.documents' => ['nullable', 'boolean'],
            'formData.keys' => ['nullable', 'boolean'],
            'formData.distance' => ['nullable', 'numeric', 'min:0'],
            'formData.note' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['moveable' => true, 'documents' => true, 'keys' => true];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(DsOrderItem::findOrFail($id));
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
        DsOrderItem::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = DsOrderItem::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('vrm', 'like', "%{$v}%")->orWhere('pickup_postcode', 'like', "%{$v}%")->orWhere('dropoff_postcode', 'like', "%{$v}%")->orWhere('ds_order_id', $v)))
            ->when($this->filter('ds_order_id'), fn ($q, $v) => $q->where('ds_order_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.ds-order-items-index', ['rows' => $rows]);
    }
}
