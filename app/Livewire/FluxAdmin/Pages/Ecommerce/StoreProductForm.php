<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnProduct;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class StoreProductForm extends Component
{
    use WithAuthorization;

    public ?NgnProduct $product = null;

    public array $form = [];

    public function mount(?NgnProduct $product = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-ecommerce');
        $this->product = $product;

        if ($product && $product->exists) {
            $this->form = $product->getAttributes();
        } else {
            $this->form = ['is_oxford' => false, 'is_ecommerce' => false];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.sku'          => ['nullable', 'string', 'max:100'],
            'form.name'         => ['required', 'string', 'max:255'],
            'form.brand_id'     => ['nullable', 'integer', 'exists:ngn_brands,id'],
            'form.category_id'  => ['nullable', 'integer', 'exists:ngn_categories,id'],
            'form.normal_price' => ['nullable', 'numeric', 'min:0'],
            'form.pos_price'    => ['nullable', 'numeric', 'min:0'],
            'form.global_stock' => ['nullable', 'integer', 'min:0'],
            'form.is_oxford'    => ['boolean'],
            'form.is_ecommerce' => ['boolean'],
            'form.description'  => ['nullable', 'string'],
            'form.slug'         => ['nullable', 'string', 'max:255'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->product && $this->product->exists) {
            $this->product->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Product updated.');
        } else {
            NgnProduct::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Product created.');
        }

        $this->redirect(route('flux-admin.store-front.index'), navigate: true);
    }

    public function render()
    {
        $brands     = NgnBrand::orderBy('name')->get(['id', 'name']);
        $categories = NgnCategory::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.ecommerce.store-product-form', compact('brands', 'categories'));
    }
}
