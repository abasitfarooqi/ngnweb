<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Active rentals — Flux Admin')]
class ActiveRentalsIndex extends Component
{
    use WithAuthorization;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function render()
    {
        $activeBookings = RentingBooking::with([
            'customer',
            'rentingBookingItems.motorbike',
            'bookingInvoices' => fn ($q) => $q->where('is_paid', false)->orderByDesc('invoice_date'),
        ])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', fn ($q) => $q->where('is_posted', true)->whereNull('end_date'))
            ->orderByDesc('id')
            ->get();

        $activeItems = $activeBookings->flatMap->rentingBookingItems->whereNull('end_date');
        $bookingIds = $activeBookings->pluck('id');

        $stats = [
            'active_rentals' => $activeItems->count(),
            'weekly_revenue' => (float) $activeItems->sum('weekly_rent'),
            'due_payments' => BookingInvoice::whereIn('booking_id', $bookingIds)->where('is_paid', false)->where('invoice_date', '<=', now())->count(),
            'total_deposits' => (float) $activeBookings->sum('deposit'),
            'unpaid_invoices' => (float) BookingInvoice::whereIn('booking_id', $bookingIds)->where('is_paid', false)->where('invoice_date', '<=', now())->sum('amount'),
        ];

        return view('flux-admin.pages.rentals.active-rentals-index', compact('activeBookings', 'stats'));
    }
}
