<?php

namespace App\Http\Controllers\Admin;

use App\Models\RentingBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class AdjustBookingWeekdayController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AdjustBookingWeekdayController extends Controller
{
    public function index()
    {
        $bookings = RentingBooking::with(['customer'])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', function ($query) {
                $query->where('is_posted', 1)
                    ->whereNull('end_date'); // Active booking items only
            })
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($booking) {
                return (object) [
                    'id' => $booking->id,
                    'start_date' => $booking->start_date,
                    'customer' => $booking->customer
                                    ? $booking->customer->first_name.' '.$booking->customer->last_name
                                    : '',
                ];
            });

        return view('livewire.agreements.migrated.admin.adjust_booking_weekday', [
            'title' => 'Adjust Booking Weekday',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'AdjustBookingWeekday' => false,
            ],
            'page' => 'resources/views/admin/adjust_booking_weekday.blade.php',
            'controller' => 'app/Http/Controllers/Admin/AdjustBookingWeekdayController.php',
            'bookings' => $bookings,
        ]);
    }

    public function ActiveBookingsSummary()
    {
        $bookings = RentingBooking::with(['customer'])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', function ($query) {
                $query->where('is_posted', 1)
                    ->whereNull('end_date'); // Active booking items only
            })
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($booking) {
                return (object) [
                    'id' => $booking->id,
                    'start_date' => $booking->start_date,
                    'customer' => $booking->customer
                                    ? $booking->customer->first_name.' '.$booking->customer->last_name
                                    : '',
                ];
            });

        return view('livewire.agreements.migrated.admin.active_renting_summary', [
            'title' => 'Active Bookings Summary',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'AdjustBookingWeekday' => false,
            ],
            'page' => 'resources/views/admin/active_renting_summary.blade.php',
            'controller' => 'app/Http/Controllers/Admin/AdjustBookingWeekdayController.php',
            'bookings' => $bookings,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'new_weekday' => 'required|integer|min:0|max:6',
        ]);

        $booking = DB::table('renting_bookings')->where('id', $id)->first();
        if (! $booking) {
            return redirect()->back()->withErrors(['Booking not found.']);
        }

        $originalDate = Carbon::parse($booking->start_date);

        // Get the current day of week (0 = Sunday, 6 = Saturday)
        $currentWeekday = $originalDate->dayOfWeek;

        // Difference between current and target
        $diff = $request->new_weekday - $currentWeekday;

        // Adjust within the same week
        $newDate = $originalDate->copy()->addDays($diff);

        DB::table('renting_bookings')
            ->where('id', $id)
            ->update(['start_date' => $newDate]);

        return redirect()->back()->with('success', 'Start date updated to '.$newDate->format('l, d M Y'));
    }
}
