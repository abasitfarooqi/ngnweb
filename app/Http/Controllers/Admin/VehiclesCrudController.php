<?php

namespace App\Http\Controllers\Admin;

use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;

class VehiclesCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function setup()
    {
        // CRUD::setModel(\App\Models\Motorbikes::class);
        CRUD::setModel(\App\Models\MotorbikeAnnualCompliance::class);
        CRUD::enableExportButtons();
        CRUD::setRoute(config('backpack.base.route_prefix').'/vehicle-database');
        CRUD::setEntityNameStrings('Vehicle Database', 'Vehicle Database');

        CRUD::enableDetailsRow();

        \DB::enableQueryLog();

        // Executes a SQL query joining multiple tables related to motorbikes.
        // Filters motorbikes still active in rentals (no end date).
        // Outputs compliance details and sale status.
        // Classifies motorbikes with dynamic labels based on their use or transaction state, such as "COMPANY VEHICLE," "INSTALLMENT SOLD," "AVAILABLE," "RENTAL," "SOLD," "SALE," or "MOT/SUBSCRIBER."
        $this->crud->query->leftJoin('application_items', 'motorbike_annual_compliance.motorbike_id', '=', 'application_items.motorbike_id')
            ->leftJoin('finance_applications', 'application_items.application_id', '=', 'finance_applications.id')
            ->leftJoin('customers as app_customers', 'finance_applications.customer_id', '=', 'app_customers.id')
            ->leftJoin('renting_booking_items', 'motorbike_annual_compliance.motorbike_id', '=', 'renting_booking_items.motorbike_id')
            ->leftJoin('renting_bookings', 'renting_booking_items.booking_id', '=', 'renting_bookings.id')
            ->leftJoin('customers as rent_customers', 'renting_bookings.customer_id', '=', 'rent_customers.id')
            ->leftJoin('motorbikes_sale', 'motorbike_annual_compliance.motorbike_id', '=', 'motorbikes_sale.motorbike_id')
            ->leftJoin('company_vehicles', 'motorbike_annual_compliance.motorbike_id', '=', 'company_vehicles.motorbike_id')
            ->leftJoin('motorbikes', 'motorbike_annual_compliance.motorbike_id', '=', 'motorbikes.id')
            ->leftJoin('recovered_motorbikes', 'motorbike_annual_compliance.motorbike_id', '=', 'recovered_motorbikes.motorbike_id')
            ->leftJoin('claim_motorbikes', 'motorbike_annual_compliance.motorbike_id', '=', 'claim_motorbikes.motorbike_id')
            // ->whereNull('renting_booking_items.end_date')

            ->select('motorbike_annual_compliance.*', 'motorbikes_sale.is_sold', \DB::raw("
            CASE
                WHEN company_vehicles.motorbike_id IS NOT NULL THEN 'COMPANY VEHICLE'
                WHEN application_items.motorbike_id IS NOT NULL AND finance_applications.log_book_sent = true THEN CONCAT('INSTALLMENT SOLD: ', app_customers.first_name, ' ', app_customers.last_name)
                WHEN application_items.motorbike_id IS NOT NULL AND
                        recovered_motorbikes.motorbike_id IS NULL
                    THEN
                        CONCAT('INSTALLMENT: ', app_customers.first_name, ' ', app_customers.last_name)
                WHEN application_items.motorbike_id IS NOT NULL AND
                    recovered_motorbikes.motorbike_id IS NOT NULL AND
                    recovered_motorbikes.case_date > application_items.updated_at THEN
                'RECOVERED'
                WHEN renting_booking_items.motorbike_id IS NOT NULL AND renting_booking_items.end_date IS NULL THEN CONCAT('RENTAL: ', rent_customers.first_name, ' ', rent_customers.last_name)
                WHEN renting_booking_items.motorbike_id IS NOT NULL AND renting_booking_items.end_date IS NOT NULL THEN CONCAT('RENTED: ', rent_customers.first_name, ' ', rent_customers.last_name)
                WHEN claim_motorbikes.motorbike_id IS NOT NULL THEN 'CLAIM'
                WHEN application_items.motorbike_id IS NOT NULL AND
                    recovered_motorbikes.motorbike_id IS NOT NULL AND
                    renting_booking_items.motorbike_id IS NOT NULL AND
                    renting_booking_items.start_date < recovered_motorbikes.case_date AND
                    renting_booking_items.end_date IS NOT NULL
                    THEN 'AVAILABLE'
                WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = true THEN 'SOLD'
                WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN 'SALE'

                ELSE 'MOT/SUBSCRIBER'
            END as association_status
        "));
        // from user removed: renting_booking_items.end_date IS NOT NULL
        // ADD: WHEN renting_booking_items.motorbike_id IS NOT NULL AND renting_booking_items.end_date IS NULL THEN CONCAT('RENTAL: ', rent_customers.first_name, ' ', rent_customers.last_name)

        $log = \DB::getQueryLog();
        \Log::info($log);

        $this->crud->query->orderByRaw('motorbike_annual_compliance.id');
    }

    protected function setupListOperation()
    {
        CRUD::removeColumn('motorbike_id');

        CRUD::addFilter(
            [
                'name' => 'motorbike_reg_no',
                'type' => 'text',
                'label' => 'REG. NO',
                'sortable' => false,
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'motorbike', function ($query) use ($value) {
                    $query->where('reg_no', 'LIKE', "%$value%");
                });
            }
        );

        $this->crud->addFilter(
            [
                'name' => 'road_tax_status',
                'type' => 'select2',
                'label' => 'Road Tax',
            ],
            [
                'SORN' => 'SORN',
                'TAXED' => 'TAXED',
                'UNTAXED' => 'UNTAXED',
            ],
            function ($value) {
                $this->crud->addClause('where', 'road_tax_status', $value);
            }
        );

        $this->crud->addFilter(
            [
                'name' => 'mot_status',
                'type' => 'select2',
                'label' => 'MOT Status',
            ],
            [
                'No details held by DVLA' => 'No details held by DVLA',
                'Valid' => 'Valid',
                'Not valid' => 'Not valid',
            ],
            function ($value) {
                $this->crud->addClause('where', 'mot_status', $value);
            }
        );

        $this->crud->addFilter(
            [
                'name' => 'association_status',
                'type' => 'dropdown',
                'label' => 'Status',
            ],
            [
                'MOT/SUBSCRIBER' => 'MOT/SUBSCRIBER',
                'AVAILABLE' => 'AVAILABLE',
                'INSTALLMENT SOLD' => 'INSTALLMENT SOLD',
                'INSTALLMENT' => 'INSTALLMENT',
                'RENTAL' => 'RENTAL',
                'RENTED' => 'RENTED', // Added RENTED option
                'SALE' => 'SALE',
                'SOLD' => 'SOLD',
                'CLAIM' => 'CLAIM',
                'COMPANY VEHICLE' => 'COMPANY VEHICLE',
            ],
            function ($value) {
                $this->crud->addClause('where', \DB::raw("
                    CASE
                        WHEN company_vehicles.motorbike_id IS NOT NULL THEN 'COMPANY VEHICLE'
                        WHEN application_items.motorbike_id IS NOT NULL AND finance_applications.log_book_sent = true THEN 'INSTALLMENT SOLD'
                        WHEN application_items.motorbike_id IS NOT NULL AND recovered_motorbikes.motorbike_id IS NOT NULL THEN 'AVAILABLE'
                        WHEN application_items.motorbike_id IS NOT NULL THEN 'INSTALLMENT'
                        WHEN renting_booking_items.motorbike_id IS NOT NULL
                            AND renting_booking_items.end_date IS NULL
                            AND renting_booking_items.start_date IS NOT NULL THEN 'RENTAL'  -- Rentals where end_date is NULL and start_date exists
                        WHEN renting_booking_items.motorbike_id IS NOT NULL
                            AND renting_booking_items.end_date IS NOT NULL
                            AND renting_booking_items.start_date IS NOT NULL THEN 'RENTED'  -- Rented with end_date not NULL and start_date exists
                        WHEN renting_booking_items.motorbike_id IS NOT NULL
                            AND renting_booking_items.end_date IS NULL
                            AND renting_booking_items.start_date IS NULL THEN 'RENTAL'  -- Add logic to handle cases with NULL start_date
                        WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = true THEN 'SOLD'
                        WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN 'SALE'
                        WHEN claim_motorbikes.motorbike_id IS NOT NULL THEN 'CLAIM'
                        ELSE 'MOT/SUBSCRIBER'
                    END
                "), $value);
            }
        );

        CRUD::column('motorbike.reg_no')->label('REG. No');

        CRUD::column('motorbike.make')->label('MAKE');

        CRUD::column('motorbike.model')->label('MODEL');

        CRUD::column('motorbike.year')->label('YEAR');

        CRUD::column('motorbike.engine')->label('ENGINE');

        CRUD::column('motorbike.color')->label('COLOR');

        CRUD::addColumn([
            'name' => 'association_status',
            'label' => 'STATUS',
            'type' => 'closure',
            'function' => function ($entry) {
                if ($entry->association_status === 'MOT/SUBSCRIBER') {
                    return '<span style="font-weight:bold; background-color:#FFFFED; padding:10px ">'.$entry->association_status.'</span>';
                } elseif ($entry->association_status === 'COMPANY VEHICLE') {
                    return '<span style="font-weight:bold; background-color:#FFB6C1; padding:10px ">'.$entry->association_status.'</span>';
                } elseif (strpos($entry->association_status, 'INSTALLMENT SOLD') !== false) {
                    return '<span style="font-weight:bold; background-color:#FFDBBB; padding:10px ">INSTALLMENT</span> <span style="font-weight:bold; background-color:#d96868; padding:10px ">SOLD</span>';
                } elseif (strpos($entry->association_status, 'INSTALLMENT') !== false) {
                    return '<span style="font-weight:bold; background-color:lightgreen; padding:10px ">'.'INSTALLMENT'.'</span>';
                } elseif (strpos($entry->association_status, 'RENTAL') !== false) {
                    return '<span style="font-weight:bold; background-color:lightblue; padding:10px "> RENTAL </span>';
                } elseif (strpos($entry->association_status, 'RENTED') !== false) {
                    return '<span style="font-weight:bold; background-color:GRAY; padding:10px "> RENTED </span>';
                } elseif ($entry->association_status === 'SALE') {
                    return '<span style="font-weight:bold; background-color:#FFDBBB; padding:10px ">SALE</span>';
                } elseif ($entry->association_status === 'SOLD') {
                    return '<span style="font-weight:bold; background-color:#FFDBBB; padding:10px ">SALE</span> <span style="font-weight:bold; background-color:#d96868; padding:10px ">SOLD</span>';
                } elseif ($entry->association_status === 'AVAILABLE') {
                    return '<span style="font-weight:bold; background-color:#DDF3DE; padding:10px ">AVAILABLE</span>';
                } elseif ($entry->association_status === 'CLAIM') {
                    return '<span style="font-weight:bold; background-color:yellow; padding:10px ">CLAIM</span>';
                }

                return $entry->association_status;
            },
            'escaped' => false,
        ]);

        CRUD::addColumn([
            'name' => 'road_tax_status',
            'label' => 'ROAD TAX',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'tax_due_date',
            'label' => 'TAX DUE',
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name' => 'mot_status',
            'label' => 'MOT',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'mot_due_date',
            'label' => 'MOT DUE',
            'type' => 'date',
        ]);
        // // //
        CRUD::enableExportButtons();
        // // //
    }

    public function showDetailsRow($id)
    {
        \Log::info('Showing details for vehicle compliance id: '.$id);

        // $motorbike_id  = MotorbikeAnnualCompliance::class . getMotorbikeId($id);
        $motorbike_id = MotorbikeAnnualCompliance::where('id', $id)->first()->motorbike_id;

        // TOTAL DEBT (PCN)
        $pcn_debt = DB::table('pcn_cases as pc')
            ->select(
                'pc.motorbike_id as MOTORBIKE_ID',
                DB::raw('COALESCE(sum(reduced_amount), 0) as TOTAL_REDUCED'),
                DB::raw('COALESCE(sum(full_amount), 0) as FULL_AMOUNT'),
                DB::raw('COALESCE(count(1), 0) as TOTAL_PCN'),
                DB::raw('(
                SELECT COALESCE(SUM(reduced_amount), 0)
                FROM pcn_cases
                WHERE motorbike_id = pc.motorbike_id AND isClosed = true
            ) as TOTAL_PAID'),
                DB::raw('(
                SELECT SUM(additional_fee)
                FROM pcn_case_updates
                WHERE case_id IN (
                    SELECT id
                    FROM pcn_cases
                    WHERE motorbike_id = pc.motorbike_id
                ) AND is_paid_by_owner = true
            ) as TOTAL_ADDITIONAL'),
                DB::raw('(
                SELECT COUNT(1)
                FROM pcn_cases
                WHERE motorbike_id = pc.motorbike_id AND isClosed = true
            ) as TOTAL_CLOSED'),
                DB::raw('(
                SELECT SUM(reduced_amount)
                FROM pcn_cases pc
                WHERE id IN (
                    SELECT DISTINCT(pcn_case_updates.case_id)
                    FROM pcn_case_updates
                    INNER JOIN pcn_cases ON pcn_cases.id = pcn_case_updates.case_id
                    WHERE pcn_case_updates.is_paid_by_owner = true
                    AND pcn_case_updates.is_paid_by_keeper = false
                    AND pcn_cases.motorbike_id = '.$motorbike_id.'
                    AND pcn_cases.isClosed = false
                )
            ) as PAID_BY_NGN'),
                DB::raw('(
                SELECT COUNT(reduced_amount) * 25
                FROM pcn_cases pc
                WHERE id IN (
                    SELECT DISTINCT(pcn_case_updates.case_id)
                    FROM pcn_case_updates
                    INNER JOIN pcn_cases ON pcn_cases.id = pcn_case_updates.case_id
                    WHERE pcn_case_updates.is_paid_by_owner = true
                    AND pcn_case_updates.is_paid_by_keeper = false
                    AND pcn_cases.motorbike_id = '.$motorbike_id.'
                    AND pcn_cases.isClosed = false
                )
            ) as ADMIN_FEE'),
                DB::raw('(
                SELECT COUNT(reduced_amount)
                FROM pcn_cases pc
                WHERE id IN (
                    SELECT DISTINCT(pcn_case_updates.case_id)
                    FROM pcn_case_updates
                    INNER JOIN pcn_cases ON pcn_cases.id = pcn_case_updates.case_id
                    WHERE pcn_case_updates.is_paid_by_owner = true
                    AND pcn_case_updates.is_paid_by_keeper = false
                    AND pcn_cases.motorbike_id = '.$motorbike_id.'
                    AND pcn_cases.isClosed = false
                )
            ) as TOTAL_NGN_PAID')
            )
            ->where('pc.motorbike_id', $motorbike_id)
            ->groupBy('pc.motorbike_id')
            ->first();

        // PCN LIST
        $pcn_list = DB::table('pcn_cases as pc')
            ->select(
                'pc.pcn_number as PCN_NUMBER',
                'pc.date_of_contravention as DOC',
                'pc.time_of_contravention as TOC',
                'pc.motorbike_id as MOTORBIKE_ID',
                'pc.isClosed as IS_CLOSED',
                'pc.full_amount as FULL_AMOUNT',
                'pc.reduced_amount as TOTAL_REDUCED',
            )
            ->where('pc.motorbike_id', $motorbike_id)
            ->get();

        // $pcn_debt = DB::table('pcn_cases as pc')
        //     ->select(
        //         'pc.motorbike_id as MOTORBIKE_ID',
        //         DB::raw('COALESCE(sum(reduced_amount), 0) as TOTAL_REDUCED'),
        //         DB::raw('COALESCE(sum(full_amount), 0) as FULL_AMOUNT'),
        //         DB::raw('COALESCE(count(1), 0) as TOTAL_PCN'),
        //         DB::raw('(
        //     SELECT COALESCE(SUM(reduced_amount), 0)
        //     FROM pcn_cases
        //     WHERE motorbike_id = pc.motorbike_id AND isClosed = true
        // ) as TOTAL_PAID'),
        //         DB::raw('(
        //     SELECT SUM(additional_fee)
        //     FROM pcn_case_updates
        //     WHERE case_id IN (
        //         SELECT id
        //         FROM pcn_cases
        //         WHERE motorbike_id = pc.motorbike_id
        //     ) AND is_paid_by_owner = true
        // ) as TOTAL_ADDITIONAL'),
        //         DB::raw('(
        //     SELECT COUNT(1)
        //     FROM pcn_cases
        //     WHERE motorbike_id = pc.motorbike_id AND isClosed = true
        // ) as TOTAL_CLOSED'),
        //         DB::raw('(
        //     SELECT SUM(reduced_amount)
        //     FROM pcn_cases pc
        //     WHERE id IN (
        //         SELECT DISTINCT(pcn_case_updates.case_id)
        //         FROM pcn_case_updates
        //         INNER JOIN pcn_cases ON pcn_cases.id = pcn_case_updates.case_id
        //         WHERE pcn_case_updates.is_paid_by_owner = true
        //         AND pcn_case_updates.is_paid_by_keeper = false
        //         AND pcn_cases.motorbike_id = 43
        //         AND pcn_cases.isClosed = false
        //     )
        // ) as PAID_BY_NGN'),
        //         DB::raw('(
        //     SELECT COUNT(reduced_amount) * 25
        //     FROM pcn_cases pc
        //     WHERE id IN (
        //         SELECT DISTINCT(pcn_case_updates.case_id)
        //         FROM pcn_case_updates
        //         INNER JOIN pcn_cases ON pcn_cases.id = pcn_case_updates.case_id
        //         WHERE pcn_case_updates.is_paid_by_owner = true
        //         AND pcn_case_updates.is_paid_by_keeper = false
        //         AND pcn_cases.motorbike_id = ' . $motorbike_id . '
        //         AND pcn_cases.isClosed = false
        //     )
        // ) as ADMIN_FEE'),
        //         DB::raw('(
        //     SELECT COUNT(reduced_amount)
        //     FROM pcn_cases pc
        //     WHERE id IN (
        //         SELECT DISTINCT(pcn_case_updates.case_id)
        //         FROM pcn_case_updates
        //         INNER JOIN pcn_cases ON pcn_cases.id = pcn_case_updates.case_id
        //         WHERE pcn_case_updates.is_paid_by_owner = true
        //         AND pcn_case_updates.is_paid_by_keeper = false
        //         AND pcn_cases.motorbike_id = ' . $motorbike_id . '
        //         AND pcn_cases.isClosed = false
        //     )
        // ) as TOTAL_NGN_PAID')
        //     )
        //     ->where('pc.motorbike_id', $motorbike_id)
        //     ->groupBy('pc.motorbike_id')
        //     ->first();

        \Log::info($pcn_list);

        // Current Keeper
        $current_keeper = DB::table('motorbikes as m')
            ->select('m.id as MOTORBIKE_ID', \DB::raw('"COMPANY VEHICLE" as VEH_CLASS'), \DB::raw('"NOT SPECIFIED" as USER'))
            ->join('company_vehicles as cv', 'cv.motorbike_id', '=', 'm.id')
            ->union(DB::table('motorbikes as m')
                ->select('m.id as MOTORBIKE_ID', \DB::raw('"INSTALMENT" as VEH_CLASS'), \DB::raw('
                (
                    SELECT customers.first_name FROM customers WHERE id = (
                        SELECT fa.customer_id FROM finance_applications fa WHERE fa.id = ai.application_id
                    )
                ) as USER
            '))
                ->join('application_items as ai', 'ai.motorbike_id', '=', 'm.id')
                ->whereNotIn('m.id', function ($query) {
                    $query->select('motorbike_id')->from('recovered_motorbikes');
                }))
            ->union(DB::table('motorbikes as m')
                ->select('m.id as MOTORBIKE_ID', \DB::raw('"RENTALS" as VEH_CLASS'), \DB::raw('
                (
                    SELECT customers.first_name FROM customers WHERE id = (
                        SELECT rb.customer_id FROM renting_bookings rb WHERE rb.id = rbi.booking_id
                    )
                ) as USER
            '))
                ->join('renting_booking_items as rbi', function ($join) {
                    $join->on('rbi.motorbike_id', '=', 'm.id')
                        ->whereNull('rbi.end_date');
                }))
            ->union(DB::table('motorbikes as m')
                ->select('m.id as MOTORBIKE_ID', \DB::raw('"USED SALE" as VEH_CLASS'), \DB::raw('"NOT SPECIFIED" as USER'))
                ->join('motorbikes_sale as ms', function ($join) {
                    $join->on('m.id', '=', 'ms.motorbike_id')
                        ->where('ms.is_sold', false);
                }))
            ->union(DB::table('motorbikes as m')
                ->select('m.id as MOTORBIKE_ID', \DB::raw('"REPAIR" as VEH_CLASS'), 'mr.fullname as USER')
                ->join('motorbikes_repair as mr', function ($join) {
                    $join->on('mr.motorbike_id', '=', 'm.id')
                        ->where('mr.is_returned', true);
                }))
            ->union(DB::table('motorbikes as m')
                ->select('m.id as MOTORBIKE_ID', \DB::raw('"CLAIM" as VEH_CLASS'), 'cm.fullname as USER')
                ->join('claim_motorbikes as cm', function ($join) {
                    $join->on('cm.motorbike_id', '=', 'm.id')
                        ->where('cm.is_returned', false);
                }))
            ->get()
            ->where('MOTORBIKE_ID', $motorbike_id);

        \Log::info($current_keeper);

        return view('vendor.backpack.crud.details_row_veh', [
            'pcn_debt' => $pcn_debt,
            'id' => $id,
            'pcn_list' => $pcn_list,
            'current_keeper' => $current_keeper,
        ]);
    }
}
