<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Models\RentingBooking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class RentalShow extends Component
{
    public RentingBooking $booking;

    #[Url]
    public string $activeTab = 'items';

    public function mount(RentingBooking $booking): void
    {
        $this->booking = $booking->load(['customer', 'rentingBookingItems.motorbike']);
    }

    public function getTitle(): string
    {
        return "Booking #{$this->booking->id} — Flux Admin";
    }

    public function render()
    {
        return view('flux-admin.pages.rentals.show');
    }
}
