<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use Livewire\Attributes\Title;

#[Title('Spare parts orders — Flux Admin')]
class SparePartOrderIndex extends EcOrderIndex
{
    public string $listTitle = 'Spare parts orders';

    public string $listDescription = 'Spare parts checkout orders from the website.';

    public string $listIndexRoute = 'flux-admin.spare-part-orders.index';

    protected string $lineTypeFilter = 'sparepart';

    public function mount(): void
    {
        parent::mount();
        $this->exportFilename = 'spare-part-orders';
    }
}
