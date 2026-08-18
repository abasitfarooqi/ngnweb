<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\OxfordProducts;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Oxford product — Flux Admin')]
class OxfordProductForm extends Component
{
    use WithAuthorization;

    public ?OxfordProducts $oxfordProduct = null;

    public array $form = [];

    public function mount(?OxfordProducts $oxfordProduct = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->oxfordProduct = $oxfordProduct;

        if ($oxfordProduct && $oxfordProduct->exists) {
            $this->form = $oxfordProduct->getAttributes();
        } else {
            $this->form = [
                'vatable' => false,
                'obsolete' => false,
                'dead' => false,
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.sku' => ['required', 'string', 'max:100', Rule::unique('oxford_products', 'sku')->ignore($this->oxfordProduct?->id)],
            'form.description' => ['nullable', 'string', 'max:500'],
            'form.ean' => ['nullable', 'string', 'max:50'],
            'form.brand' => ['nullable', 'string', 'max:120'],
            'form.supplier' => ['nullable', 'string', 'max:120'],
            'form.supplier_code' => ['nullable', 'string', 'max:100'],
            'form.rrp_inc_vat' => ['nullable', 'numeric', 'min:0'],
            'form.rrp_less_vat' => ['nullable', 'numeric', 'min:0'],
            'form.cost_price' => ['nullable', 'numeric', 'min:0'],
            'form.stock' => ['nullable', 'integer'],
            'form.catford_stock' => ['nullable', 'integer'],
            'form.colour' => ['nullable', 'string', 'max:100'],
            'form.variation' => ['nullable', 'string', 'max:100'],
            'form.vatable' => ['nullable', 'boolean'],
            'form.obsolete' => ['nullable', 'boolean'],
            'form.dead' => ['nullable', 'boolean'],
        ]);

        $payload = collect($this->form)->only([
            'sku', 'description', 'ean', 'brand', 'supplier', 'supplier_code',
            'rrp_inc_vat', 'rrp_less_vat', 'cost_price', 'stock', 'catford_stock',
            'colour', 'variation', 'vatable', 'obsolete', 'dead',
        ])->all();

        foreach (['vatable', 'obsolete', 'dead'] as $flag) {
            $payload[$flag] = (bool) ($payload[$flag] ?? false);
        }

        if ($this->oxfordProduct && $this->oxfordProduct->exists) {
            $this->oxfordProduct->update($payload);
            $message = 'Oxford product updated.';
        } else {
            OxfordProducts::create($payload);
            $message = 'Oxford product created.';
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: $message);
        $this->redirect(route('flux-admin.oxford-products.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.inventory.oxford-product-form');
    }
}
