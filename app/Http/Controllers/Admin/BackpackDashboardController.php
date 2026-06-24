<?php

namespace App\Http\Controllers\Admin;

use App\Support\FluxAdminDashboardStats;
use Backpack\CRUD\app\Http\Controllers\AdminController as BackpackAdminController;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class BackpackDashboardController extends BackpackAdminController
{
    /**
     * Show the NGN admin dashboard (rental payments, fleet counts, chart).
     * Same data as legacy admin dashboard, rendered inside Backpack layout.
     */
    public function dashboard(): View
    {
        $this->data['title'] = trans('backpack::base.dashboard');
        $this->data['toDay'] = Carbon::now();

        $fleet = FluxAdminDashboardStats::fleetAndPayments();

        $this->data['count'] = $fleet['payment_count'];
        $this->data['rpayments'] = $fleet['outstanding_total'];
        $this->data['rrpayments'] = $fleet['outstanding_rentals'];
        $this->data['ddpayments'] = $fleet['outstanding_deposits'];
        $this->data['rentaldata'] = $fleet['fleet_chart_values'];
        $this->data['forRentCount'] = $fleet['fleet_counts']['for_rent'];
        $this->data['rentedCount'] = $fleet['fleet_counts']['rented'];
        $this->data['forSaleCount'] = $fleet['fleet_counts']['for_sale'];
        $this->data['soldCount'] = $fleet['fleet_counts']['sold'];
        $this->data['repairsCount'] = $fleet['fleet_counts']['repairs'];
        $this->data['catBCount'] = $fleet['fleet_counts']['cat_b'];
        $this->data['claimInProgressCount'] = $fleet['fleet_counts']['claim'];
        $this->data['impoundedCount'] = $fleet['fleet_counts']['impounded'];
        $this->data['accidentCount'] = $fleet['fleet_counts']['accident'];
        $this->data['missingCount'] = $fleet['fleet_counts']['missing'];
        $this->data['stolenCount'] = $fleet['fleet_counts']['stolen'];
        $this->data['taxDue'] = $fleet['tax_due'];
        $this->data['motDue'] = $fleet['mot_due'];

        return view(backpack_view('dashboard'), $this->data);
    }
}
