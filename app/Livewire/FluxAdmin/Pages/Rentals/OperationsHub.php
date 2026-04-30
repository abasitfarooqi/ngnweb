<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental operations — Flux Admin')]
class OperationsHub extends Component
{
    use WithAuthorization;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function render()
    {
        return view('flux-admin.pages.rentals.operations-hub');
    }
}
