<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Sale — Flux Admin')]
class SaleOperationsHub extends Component
{
    use WithAuthorization;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-vehicles');
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.sale-operations-hub');
    }
}
