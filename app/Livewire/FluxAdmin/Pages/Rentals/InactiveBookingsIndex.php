<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('flux-admin.layouts.app')]
#[Title('Inactive bookings — Flux Admin')]
class InactiveBookingsIndex extends RentalIndex
{
    public function mount(): void
    {
        $this->scope = 'inactive';
        parent::mount();
    }
}
