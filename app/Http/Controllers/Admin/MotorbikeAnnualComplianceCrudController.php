<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class MotorbikeAnnualComplianceCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\MotorbikeAnnualCompliance::class);
        CRUD::enableExportButtons();
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-annual-compliance');
        CRUD::setEntityNameStrings('Vehicle Database', 'Vehicles Database');

    }

    protected function setupListOperation()
    {
        CRUD::removeColumn('motorbike_id');

        CRUD::addFilter(
            [
                'name' => 'motorbike_reg_no',
                'type' => 'text',
                'label' => 'REG. NO',
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
                'Unassociated' => 'Unassociated',
                'MOT' => 'MOT',
                'SUBSCRIBER' => 'SUBSCRIBER',
                'INSTALLMENT TRANSFERRED' => 'INSTALLMENT TRANSFERRED',
                'INSTALLMENT' => 'INSTALLMENT',
                'RENTAL' => 'RENTAL',
                'SALE' => 'SALE',
                'SOLD' => 'SOLD',
                'COMPANY VEHICLE' => 'COMPANY VEHICLE',
            ],
            function ($value) {
                $this->crud->addClause('where', \DB::raw("
                    CASE
                        WHEN company_vehicles.motorbike_id IS NOT NULL THEN 'COMPANY VEHICLE'
                        WHEN application_items.motorbike_id IS NOT NULL AND finance_applications.log_book_sent = true THEN 'INSTALLMENT TRANSFERRED'
                        WHEN application_items.motorbike_id IS NOT NULL THEN 'INSTALLMENT'
                        WHEN renting_booking_items.motorbike_id IS NOT NULL THEN 'RENTAL'
                        WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = true THEN 'SOLD'
                        WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN 'SALE'
                        ELSE 'Unassociated'
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

        // CRUD::addColumn([
        //     'name' => 'association_status',
        //     'label' => 'Association Status',
        //     'type' => 'custom_html',
        //     'function' => function ($entry) {
        //         return $entry->association_status;
        //     },
        // ]);

        CRUD::addColumn([
            'name' => 'association_status',
            'label' => 'STATUS',
            'type' => 'closure',
            'function' => function ($entry) {
                if ($entry->association_status === 'Unassociated') {
                    return '<span style="font-weight:bold; background-color:#FFFFED; padding:10px ">'.$entry->association_status.'</span>';
                } elseif ($entry->association_status === 'COMPANY VEHICLE') {
                    return '<span style="font-weight:bold; background-color:#FFB6C1; padding:10px ">'.$entry->association_status.'</span>';
                } elseif (strpos($entry->association_status, 'INSTALLMENT TRANSFERRED') !== false) {
                    return '<span style="font-weight:bold; background-color:#FFDBBB; padding:10px ">'.$entry->association_status.'</span>';
                } elseif (strpos($entry->association_status, 'INSTALLMENT') !== false) {
                    return '<span style="font-weight:bold; background-color:#DAF7A6; padding:10px ">'.$entry->association_status.'</span>';
                } elseif (strpos($entry->association_status, 'RENTAL') !== false) {
                    return '<span style="font-weight:bold; background-color:lightblue; padding:10px ">'.$entry->association_status.'</span>';
                } elseif ($entry->association_status === 'SALE') {
                    return '<span style="font-weight:bold; background-color:#FFDBBB; padding:10px ">SALE</span>';
                } elseif ($entry->association_status === 'SOLD') {
                    return '<span style="font-weight:bold; background-color:#FFDBBB; padding:10px ">SALE</span> <span style="font-weight:bold; background-color:#d96868; padding:10px ">SOLD</span>';
                }

                return $entry->association_status;
            },
            'escaped' => false,
        ]);

        // SELECT
        //     motorbike_annual_compliance.*,
        //     motorbikes_sale.is_sold,
        //     CASE
        //         WHEN application_items.motorbike_id IS NOT NULL THEN CONCAT('INSTALLMENT: ', app_customers.first_name, ' ', app_customers.last_name)
        //         WHEN renting_booking_items.motorbike_id IS NOT NULL THEN CONCAT('RENTAL: ', rent_customers.first_name, ' ', rent_customers.last_name)
        //         WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = TRUE THEN 'GONE'
        //         WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN 'SOLD'
        //         ELSE 'Unassociated'
        //     END AS association_status
        // FROM
        //     motorbike_annual_compliance
        // LEFT JOIN
        //     application_items ON motorbike_annual_compliance.motorbike_id = application_items.motorbike_id
        // LEFT JOIN
        //     finance_applications ON application_items.application_id = finance_applications.id
        // LEFT JOIN
        //     customers AS app_customers ON finance_applications.customer_id = app_customers.id
        // LEFT JOIN
        //     renting_booking_items ON motorbike_annual_compliance.motorbike_id = renting_booking_items.motorbike_id
        // LEFT JOIN
        //     renting_bookings ON renting_booking_items.booking_id = renting_bookings.id
        // LEFT JOIN
        //     customers AS rent_customers ON renting_bookings.customer_id = rent_customers.id
        // LEFT JOIN
        //     motorbikes_sale ON motorbike_annual_compliance.motorbike_id = motorbikes_sale.motorbike_id;

        $this->crud->query->leftJoin('application_items', 'motorbike_annual_compliance.motorbike_id', '=', 'application_items.motorbike_id')
            ->leftJoin('finance_applications', 'application_items.application_id', '=', 'finance_applications.id')
            ->leftJoin('customers as app_customers', 'finance_applications.customer_id', '=', 'app_customers.id')
            ->leftJoin('renting_booking_items', 'motorbike_annual_compliance.motorbike_id', '=', 'renting_booking_items.motorbike_id')
            ->leftJoin('renting_bookings', 'renting_booking_items.booking_id', '=', 'renting_bookings.id')
            ->leftJoin('customers as rent_customers', 'renting_bookings.customer_id', '=', 'rent_customers.id')
            ->leftJoin('motorbikes_sale', 'motorbike_annual_compliance.motorbike_id', '=', 'motorbikes_sale.motorbike_id')
            ->leftJoin('company_vehicles', 'motorbike_annual_compliance.motorbike_id', '=', 'company_vehicles.motorbike_id')
            ->select('motorbike_annual_compliance.*', 'motorbikes_sale.is_sold', \DB::raw('
            CASE
                WHEN company_vehicles.motorbike_id IS NOT NULL THEN "COMPANY VEHICLE"
                WHEN application_items.motorbike_id IS NOT NULL AND finance_applications.log_book_sent = true THEN CONCAT("INSTALLMENT TRANSFERRED: ", app_customers.first_name, " ", app_customers.last_name)
                WHEN application_items.motorbike_id IS NOT NULL THEN CONCAT("INSTALLMENT: ", app_customers.first_name, " ", app_customers.last_name)
                WHEN renting_booking_items.motorbike_id IS NOT NULL THEN CONCAT("RENTAL: ", rent_customers.first_name, " ", rent_customers.last_name)
                WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = true THEN "SOLD"
                WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN "SALE"
                ELSE "Unassociated"
            END as association_status
        '));

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
}
