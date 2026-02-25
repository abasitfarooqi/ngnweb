<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use Illuminate\Routing\Controller;

/**
 * Class ActiveRentingController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ActiveRentingController extends Controller
{
    public function index()
    {
        // Get active bookings with their items
        $activeBookings = RentingBooking::with([
            'customer',
            'rentingBookingItems.motorbike',
            'bookingInvoices' => function ($query) {
                $query->where('is_paid', false)
                    ->orderBy('invoice_date', 'desc');
            },
        ])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', function ($query) {
                $query->where('is_posted', true)
                    ->whereNull('end_date');
            })
            ->get();

        // Calculate statistics
        $stats = [
            'active_rentals' => $activeBookings->flatMap->rentingBookingItems
                ->whereNull('end_date')
                ->count(),
            'weekly_revenue' => $activeBookings->flatMap->rentingBookingItems
                ->whereNull('end_date')
                ->sum('weekly_rent'),
            'due_payments' => BookingInvoice::whereIn('booking_id', $activeBookings->pluck('id'))
                ->where('is_paid', false)
                ->where('invoice_date', '<=', now())
                ->count(),
            'total_deposits' => $activeBookings->sum('deposit'),
            'unpaid_invoices' => BookingInvoice::whereIn('booking_id', $activeBookings->pluck('id'))
                ->where('is_paid', false)
                ->where('invoice_date', '<=', now())
                ->sum('amount'),
            'booking_ids' => $activeBookings->flatMap->rentingBookingItems
                ->whereNull('end_date')
                ->pluck('booking_id'),
        ];

        return view('admin.active_renting', [
            'title' => 'Active Rentals',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'Active Rentals' => false,
            ],
            'stats' => $stats,
            'activeBookings' => $activeBookings,
        ]);
    }
}
