<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('flux-admin.layouts.app')]
#[Title('Inactive bookings — Flux Admin')]
class InactiveBookingsIndex extends BookingsManagementIndex
{
    public function mount(): void
    {
        parent::mount();
        $this->scope = 'inactive';
        $this->pageTitle = 'Inactive bookings';
        $this->pageDescription = 'Rental bookings whose current item has ended — click a booking to view history.';
    }
}
