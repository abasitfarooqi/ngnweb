<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnDigitalInvoiceItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Digital invoice items — Flux Admin')]
class DigitalInvoiceItemIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return NgnDigitalInvoiceItem::class; }

    protected function formRules(): array
    {
        return [
            'formData.invoice_id' => ['required', 'integer'],
            'formData.item_name' => ['required', 'string', 'max:255'],
            'formData.sku' => ['nullable', 'string', 'max:100'],
            'formData.quantity' => ['required', 'numeric', 'min:0'],
            'formData.price' => ['required', 'numeric', 'min:0'],
            'formData.discount' => ['nullable', 'numeric', 'min:0'],
            'formData.tax' => ['nullable', 'numeric', 'min:0'],
            'formData.total' => ['nullable', 'numeric', 'min:0'],
            'formData.notes' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['quantity' => 1, 'discount' => 0, 'tax' => 0];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnDigitalInvoiceItem::findOrFail($id));
        $this->showForm = true;
    }

    protected function beforeSave(array $attributes): array
    {
        $qty = (float) ($attributes['quantity'] ?? 0);
        $price = (float) ($attributes['price'] ?? 0);
        $disc = (float) ($attributes['discount'] ?? 0);
        $tax = (float) ($attributes['tax'] ?? 0);
        $attributes['total'] = max(0, ($qty * $price) - $disc + $tax);

        return $attributes;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        NgnDigitalInvoiceItem::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = NgnDigitalInvoiceItem::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('item_name', 'like', "%{$v}%")->orWhere('sku', 'like', "%{$v}%")->orWhere('invoice_id', $v)))
            ->when($this->filter('invoice_id'), fn ($q, $v) => $q->where('invoice_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.digital-invoice-items-index', ['rows' => $rows]);
    }
}
