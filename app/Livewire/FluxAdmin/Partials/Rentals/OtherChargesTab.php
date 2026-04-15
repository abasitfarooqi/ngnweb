<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\RentingOtherCharge;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class OtherChargesTab extends Component
{
    public int $bookingId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $charges = RentingOtherCharge::where('booking_id', $this->bookingId)->get();

        return view('flux-admin.partials.rentals.other-charges-tab', [
            'charges' => $charges,
        ]);
    }
}
