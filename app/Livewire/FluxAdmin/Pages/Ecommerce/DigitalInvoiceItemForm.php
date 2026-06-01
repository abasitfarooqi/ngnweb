<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnDigitalInvoiceItem;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class DigitalInvoiceItemForm extends Component
{
    use WithAuthorization;

    public ?NgnDigitalInvoiceItem $invoiceItem = null;

    public array $form = [];

    public function mount(?NgnDigitalInvoiceItem $invoiceItem = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->invoiceItem = $invoiceItem;

        if ($invoiceItem && $invoiceItem->exists) {
            $this->form = $invoiceItem->getAttributes();
        } else {
            $this->form = ['quantity' => 1, 'discount' => 0, 'tax' => 0];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.invoice_id' => ['required', 'integer'],
            'form.item_name'  => ['required', 'string', 'max:255'],
            'form.sku'        => ['nullable', 'string', 'max:100'],
            'form.quantity'   => ['required', 'numeric', 'min:0'],
            'form.price'      => ['required', 'numeric', 'min:0'],
            'form.discount'   => ['nullable', 'numeric', 'min:0'],
            'form.tax'        => ['nullable', 'numeric', 'min:0'],
            'form.total'      => ['nullable', 'numeric', 'min:0'],
            'form.notes'      => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        $qty   = (float) ($payload['quantity'] ?? 0);
        $price = (float) ($payload['price'] ?? 0);
        $disc  = (float) ($payload['discount'] ?? 0);
        $tax   = (float) ($payload['tax'] ?? 0);
        $payload['total'] = max(0, ($qty * $price) - $disc + $tax);

        if ($this->invoiceItem && $this->invoiceItem->exists) {
            $this->invoiceItem->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Item updated.');
        } else {
            NgnDigitalInvoiceItem::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Item created.');
        }

        $this->redirect(route('flux-admin.digital-invoice-items.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.ecommerce.digital-invoice-item-form');
    }
}
