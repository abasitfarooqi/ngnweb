<?php

namespace App\Livewire\Portal;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth->profile ?? null;
        $customerId = $customerAuth?->customer_id;

        $activeRental = null;
        $upcomingMOT = null;
        $upcomingDelivery = null;

        if ($customerId) {
            $activeRental = \App\Models\RentingBooking::where('customer_id', $customerId)
                ->active()
                ->with(['activeItems.motorbike'])
                ->first();

            $upcomingMOT = \App\Models\MOTBooking::where('customer_email', $customerAuth->email)
                ->where('date_of_appointment', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->orderBy('date_of_appointment')
                ->first();

            $upcomingDelivery = \App\Models\VehicleDeliveryOrder::where('email', $customerAuth->email)
                ->where('pickup_date', '>=', now())
                ->orderBy('pickup_date')
                ->first();
        }

        return view('livewire.portal.dashboard', compact('profile', 'activeRental', 'upcomingMOT', 'upcomingDelivery'))
            ->layout('components.layouts.portal');
    }
}
