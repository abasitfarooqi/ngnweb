<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT — Flux Admin')]
class MotOperationsHub extends Component
{
    use WithAuthorization;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-mot-bookings');
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.mot-operations-hub');
    }
}
