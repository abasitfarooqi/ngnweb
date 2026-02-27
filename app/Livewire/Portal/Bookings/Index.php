<?php

namespace App\Livewire\Portal\Bookings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function render()
    {
        $customer = Auth::guard('customer')->user();
        $profile  = $customer->profile;

        $motBookings = \App\Models\MOTBooking::where('customer_email', $customer->email)
            ->orderBy('date_of_appointment', 'desc')
            ->get();

        $rentals = $profile
            ? $profile->rentingBookings()->with(['activeItems.motorbike'])->orderBy('created_at', 'desc')->take(10)->get()
            : collect();

        return view('livewire.portal.bookings.index', compact('motBookings', 'rentals'))
            ->layout('components.layouts.portal');
    }
}
