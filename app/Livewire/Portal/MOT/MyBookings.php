<?php

namespace App\Livewire\Portal\MOT;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyBookings extends Component
{
    public function render()
    {
        $customer = Auth::guard('customer')->user();
        $bookings = \App\Models\MOTBooking::where('customer_email', $customer->email)
            ->orderBy('date_of_appointment', 'desc')
            ->get();

        return view('livewire.portal.mot.my-bookings', compact('bookings'))
            ->layout('components.layouts.portal');
    }
}
