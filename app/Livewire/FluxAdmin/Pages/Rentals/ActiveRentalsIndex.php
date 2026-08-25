<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RentingBooking;
use App\Support\RentalInvoiceTabData;
use Illuminate\Support\Facades\DB;
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
            'bookingInvoices' => fn ($q) => $q->orderByDesc('invoice_date'),
        ])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', fn ($q) => $q->where('is_posted', true)->whereNull('end_date'))
            ->orderByDesc('id')
            ->get();

        $activeItems = $activeBookings->flatMap->rentingBookingItems->whereNull('end_date');
        $bookingIds = $activeBookings->pluck('id');
        $outstanding = $bookingIds->isEmpty()
            ? (object) ['unpaid_invoices' => 0, 'due_payments' => 0]
            : DB::query()
                ->fromSub(RentalInvoiceTabData::outstandingByBookingSubquery(), 'os')
                ->whereIn('os.booking_id', $bookingIds)
                ->selectRaw('COALESCE(SUM(os.outstanding_amount), 0) as unpaid_invoices')
                ->selectRaw('COALESCE(SUM(os.due_invoice_count), 0) as due_payments')
                ->first();

        $stats = [
            'active_rentals' => $activeItems->count(),
            'weekly_revenue' => (float) $activeItems->sum('weekly_rent'),
            'due_payments' => (int) ($outstanding->due_payments ?? 0),
            'total_deposits' => (float) $activeBookings->sum('deposit'),
            'unpaid_invoices' => (float) ($outstanding->unpaid_invoices ?? 0),
        ];

        return view('flux-admin.pages.rentals.active-rentals-index', compact('activeBookings', 'stats'));
    }
}
