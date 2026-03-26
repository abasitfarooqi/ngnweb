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
        $profile = $customer->profile;

        $motBookings = \App\Models\MOTBooking::where('customer_email', $customer->email)
            ->orderBy('date_of_appointment', 'desc')
            ->get();

        $rentals = $profile
            ? $profile->rentingBookings()->with(['activeItems.motorbike'])->orderBy('created_at', 'desc')->take(10)->get()
            : collect();

        // Merge MOT + rental into one unified bookings collection for the tab view
        $allBookings = $motBookings->map(fn ($m) => (object) [
            'id' => 'mot-'.$m->id,
            'type' => 'MOT',
            'date' => $m->date_of_appointment,
            'status' => $m->status ?? 'Pending',
            'label' => 'MOT Appointment',
            'source' => $m,
        ])->merge(
            $rentals->map(fn ($r) => (object) [
                'id' => 'rental-'.$r->id,
                'type' => 'Rental',
                'date' => $r->start_date ?? $r->created_at,
                'status' => $r->status ?? 'Active',
                'label' => 'Rental Booking',
                'source' => $r,
            ])
        )->sortByDesc('date')->values();

        $bookings = match ($this->activeTab) {
            'upcoming' => $allBookings->filter(fn ($b) => $b->date && $b->date >= now()->toDateString()),
            'completed' => $allBookings->filter(fn ($b) => in_array(strtolower($b->status), ['completed', 'done', 'expired'])),
            default => $allBookings,
        };

        return view('livewire.portal.bookings.index', compact('bookings', 'motBookings', 'rentals'))
            ->layout('components.layouts.portal', ['title' => 'My Bookings | My Account']);
    }
}
