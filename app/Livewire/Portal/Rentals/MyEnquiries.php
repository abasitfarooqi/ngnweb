<?php

namespace App\Livewire\Portal\Rentals;

use App\Models\ServiceBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyEnquiries extends Component
{
    use WithPagination;

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $enquiries = ServiceBooking::query()
            ->whereRentalEnquiry()
            ->forPortalCustomer($customerAuth)
            ->with('conversation')
            ->latest()
            ->paginate(12);

        return view('livewire.portal.rentals.my-enquiries', compact('enquiries'))
            ->layout('components.layouts.portal', [
                'title' => 'My Rental Enquiries | My Account',
            ]);
    }
}
