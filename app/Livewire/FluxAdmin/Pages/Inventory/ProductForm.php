<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnModel;
use App\Models\NgnProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Product — Flux Admin')]
class ProductForm extends Component
{
    use WithAuthorization;

    public ?NgnProduct $product = null;

    public array $form = [];

    public function mount(?NgnProduct $product = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->product = $product?->id ? $product : null;

        if ($this->product) {
            $this->form = $this->product->getAttributes();
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.sku'              => ['nullable', 'string', 'max:100'],
            'form.ean'              => ['nullable', 'string', 'max:50'],
            'form.name'             => ['required', 'string', 'max:255'],
            'form.variation'        => ['nullable', 'string', 'max:255'],
            'form.description'      => ['nullable', 'string'],
            'form.colour'           => ['nullable', 'string', 'max:100'],
            'form.brand_id'         => ['nullable', 'integer'],
            'form.category_id'      => ['nullable', 'integer'],
            'form.model_id'         => ['nullable', 'integer'],
            'form.normal_price'     => ['nullable', 'numeric', 'min:0'],
            'form.pos_price'        => ['nullable', 'numeric', 'min:0'],
            'form.pos_vat'          => ['nullable', 'numeric', 'min:0'],
            'form.global_stock'     => ['nullable', 'integer'],
            'form.vatable'          => ['nullable', 'boolean'],
            'form.is_oxford'        => ['nullable', 'boolean'],
            'form.dead'             => ['nullable', 'boolean'],
            'form.is_ecommerce'     => ['nullable', 'boolean'],
            'form.slug'             => ['nullable', 'string', 'max:255'],
            'form.meta_title'       => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $payload = [
            'sku'              => $this->form['sku'] ?? null,
            'ean'              => $this->form['ean'] ?? null,
            'name'             => $this->form['name'],
            'variation'        => $this->form['variation'] ?? null,
            'description'      => $this->form['description'] ?? null,
            'colour'           => $this->form['colour'] ?? null,
            'brand_id'         => $this->form['brand_id'] ?: null,
            'category_id'      => $this->form['category_id'] ?: null,
            'model_id'         => $this->form['model_id'] ?: null,
            'normal_price'     => $this->form['normal_price'] ?? null,
            'pos_price'        => $this->form['pos_price'] ?? null,
            'pos_vat'          => $this->form['pos_vat'] ?? null,
            'global_stock'     => $this->form['global_stock'] ?? null,
            'vatable'          => (bool) ($this->form['vatable'] ?? false),
            'is_oxford'        => (bool) ($this->form['is_oxford'] ?? false),
            'dead'             => (bool) ($this->form['dead'] ?? false),
            'is_ecommerce'     => (bool) ($this->form['is_ecommerce'] ?? false),
            'slug'             => $this->form['slug'] ?? null,
            'meta_title'       => $this->form['meta_title'] ?? null,
            'meta_description' => $this->form['meta_description'] ?? null,
        ];

        if ($this->product) {
            $this->product->update($payload);
        } else {
            NgnProduct::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Product saved.');
        $this->redirect(route('flux-admin.inventory-products.index'), navigate: true);
    }

    public function render()
    {
        $brands     = NgnBrand::query()->orderBy('name')->get(['id', 'name']);
        $categories = NgnCategory::query()->orderBy('name')->get(['id', 'name']);
        $models     = NgnModel::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.inventory.product-form', compact('brands', 'categories', 'models'));
    }
}
