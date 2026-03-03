<?php

namespace App\Http\Controllers\Admin;

use App\Models\Motorcycle;
use App\Models\RentalPayment;
use Backpack\CRUD\app\Http\Controllers\AdminController as BackpackAdminController;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class BackpackDashboardController extends BackpackAdminController
{
    /**
     * Show the NGN admin dashboard (rental payments, fleet counts, chart).
     * Same data as legacy admin dashboard, rendered inside Backpack layout.
     */
    public function dashboard(): View
    {
        $this->data['title'] = trans('backpack::base.dashboard');

        $toDay = Carbon::now();

        $rentalpayments = RentalPayment::all()->where('outstanding', '>', 0);
        $count = $rentalpayments->count();

        $rrpayments = DB::table('rental_payments')
            ->where('payment_type', 'rental')
            ->whereNull('deleted_at')
            ->sum('outstanding');

        $ddpayments = DB::table('rental_payments')
            ->where('payment_type', 'deposit')
            ->whereNull('deleted_at')
            ->sum('outstanding');

        $totalowed = $rrpayments + $ddpayments;
        $rpayments = number_format((float) $totalowed, 2, '.', '');

        $forRentCount = Motorcycle::where('availability', 'for rent')->count();
        $rentedCount = Motorcycle::where('availability', 'rented')->count();
        $forSaleCount = Motorcycle::where('availability', 'for sale')->count();
        $soldCount = Motorcycle::where('availability', 'sold')->count();
        $repairsCount = Motorcycle::where('availability', 'repairs')->count();
        $catBCount = Motorcycle::where('availability', 'cat b')->count();
        $claimInProgressCount = Motorcycle::where('availability', 'claim in progress')->count();
        $impoundedCount = Motorcycle::where('availability', 'impounded')->count();
        $accidentCount = Motorcycle::where('availability', 'accident')->count();
        $missingCount = Motorcycle::where('availability', 'missing')->count();
        $stolenCount = Motorcycle::where('availability', 'stolen')->count();

        $rentaldata = [
            $forRentCount,
            $rentedCount,
            $forSaleCount,
            $soldCount,
            $repairsCount,
            $catBCount,
            $claimInProgressCount,
            $impoundedCount,
            $accidentCount,
            $missingCount,
            $stolenCount,
        ];

        $taxDue = Motorcycle::where('tax_due_date', '<', Carbon::now()->addDays(10))->get();
        $motDue = Motorcycle::where('mot_expiry_date', '<', Carbon::now()->addDays(10))->get();

        $this->data['toDay'] = $toDay;
        $this->data['count'] = $count;
        $this->data['rpayments'] = $rpayments;
        $this->data['rrpayments'] = $rrpayments;
        $this->data['ddpayments'] = $ddpayments;
        $this->data['rentaldata'] = $rentaldata;
        $this->data['forRentCount'] = $forRentCount;
        $this->data['rentedCount'] = $rentedCount;
        $this->data['forSaleCount'] = $forSaleCount;
        $this->data['soldCount'] = $soldCount;
        $this->data['repairsCount'] = $repairsCount;
        $this->data['catBCount'] = $catBCount;
        $this->data['claimInProgressCount'] = $claimInProgressCount;
        $this->data['impoundedCount'] = $impoundedCount;
        $this->data['accidentCount'] = $accidentCount;
        $this->data['missingCount'] = $missingCount;
        $this->data['stolenCount'] = $stolenCount;
        $this->data['taxDue'] = $taxDue;
        $this->data['motDue'] = $motDue;

        return view(backpack_view('dashboard'), $this->data);
    }
}
