<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use Livewire\Attributes\Title;

#[Title('Shop orders — Flux Admin')]
class ShopOrderIndex extends EcOrderIndex
{
    public string $listTitle = 'Shop orders';

    public string $listDescription = 'Catalogue checkout orders from the online shop.';

    public string $listIndexRoute = 'flux-admin.shop-orders.index';

    protected string $lineTypeFilter = 'catalogue';

    public function mount(): void
    {
        parent::mount();
        $this->exportFilename = 'shop-orders';
    }
}
