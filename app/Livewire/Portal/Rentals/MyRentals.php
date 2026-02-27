<?php

namespace App\Livewire\Portal\Rentals;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyRentals extends Component
{
    public function render()
    {
        $profile = Auth::guard('customer')->user()->profile;
        $rentals = $profile
            ? $profile->rentingBookings()->with(['activeItems.motorbike'])->orderBy('created_at', 'desc')->get()
            : collect();

        return view('livewire.portal.rentals.my-rentals', compact('rentals'))
            ->layout('components.layouts.portal');
    }
}
