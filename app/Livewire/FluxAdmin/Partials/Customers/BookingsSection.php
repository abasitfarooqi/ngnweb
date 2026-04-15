<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\RentingBooking;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class BookingsSection extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $bookings = RentingBooking::where('customer_id', $this->customer->id)
            ->with('rentingBookingItems')
            ->orderByDesc('id')
            ->get();

        return view('flux-admin.partials.customers.bookings-section', compact('bookings'));
    }
}
