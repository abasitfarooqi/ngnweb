<?php

namespace App\Livewire\Portal\Bookings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $activeTab = 'all';

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();
        $customerId = $customer?->customer_id;

        $motBookings = \App\Models\MOTBooking::where('customer_email', $customer->email)
            ->orderBy('date_of_appointment', 'desc')
            ->get();

        $rentals = $customerId
            ? \App\Models\RentingBooking::where('customer_id', $customerId)->with(['rentingBookingItems.motorbike'])->orderBy('created_at', 'desc')->take(10)->get()
            : collect();

        // Merge MOT + rental into one unified bookings collection for the tab view.
        // Use plain collections to avoid Eloquent collection model-key behaviour.
        $motItems = collect($motBookings->all())->map(fn ($m) => (object) [
            'id' => 'mot-'.$m->id,
            'type' => 'MOT',
            'date' => $m->date_of_appointment,
            'status' => $m->status ?? 'Pending',
            'label' => 'MOT Appointment',
            'source' => $m,
        ]);

        $rentalItems = collect($rentals->all())->map(function ($r) {
            $items = $r->rentingBookingItems ?? collect();
            $hasActiveItem = $items->contains(fn ($item) => empty($item->end_date));
            $latestEndDate = $items->whereNotNull('end_date')->sortByDesc('end_date')->first()?->end_date;

            return (object) [
                'id' => 'rental-'.$r->id,
                'type' => 'Rental',
                'date' => $hasActiveItem ? ($r->start_date ?? $r->created_at) : ($latestEndDate ?? $r->created_at),
                'status' => $hasActiveItem ? ($r->state ?? 'ACTIVE') : 'ENDED',
                'label' => 'Rental Booking',
                'source' => $r,
            ];
        });

        $allBookings = $motItems
            ->merge($rentalItems)
            ->sortByDesc('date')
            ->values();

        $bookings = match ($this->activeTab) {
            'upcoming' => $allBookings->filter(fn ($b) => $b->date && $b->date >= now()->toDateString()),
            'completed' => $allBookings->filter(fn ($b) => in_array(strtolower((string) $b->status), ['completed', 'done', 'expired', 'ended'])),
            default => $allBookings,
        };

        return view('livewire.portal.bookings.index', compact('bookings', 'motBookings', 'rentals'))
            ->layout('components.layouts.portal', ['title' => 'My Bookings | My Account']);
    }
}
