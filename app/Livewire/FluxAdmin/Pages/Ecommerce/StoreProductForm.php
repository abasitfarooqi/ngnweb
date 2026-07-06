<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Pages\Inventory\ProductForm as InventoryProductForm;

class StoreProductForm extends InventoryProductForm
{
    public function mount(?\App\Models\NgnProduct $product = null): void
    {
        $this->redirectRoute = 'flux-admin.store-front.index';
        parent::mount($product);

        if (! $this->product) {
            $this->form['is_ecommerce'] = true;
        }
    }
}
