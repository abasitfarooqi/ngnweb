<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\RentingBookingItem;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class BookingItemsTab extends Component
{
    public int $bookingId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $items = RentingBookingItem::with(['motorbike', 'user'])
            ->where('booking_id', $this->bookingId)
            ->orderByDesc('start_date')
            ->get();

        return view('flux-admin.partials.rentals.booking-items-tab', [
            'items' => $items,
        ]);
    }
}
