<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnRentingBookingRequest;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NgnRentingBookingCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnRentingBookingCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\RentingBooking::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-renting-booking');
        CRUD::setEntityNameStrings('ngn renting booking', 'ngn renting bookings');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::with(['items']);

        CRUD::addColumn(['name' => 'id', 'label' => 'Booking ID']);
        CRUD::addColumn(['name' => 'customer.first_name', 'label' => 'Customer Name']);
        CRUD::addColumn(['name' => 'start_date', 'label' => 'Booking Date']);
        CRUD::addColumn(['name' => 'due_date', 'label' => 'Next Due Date']);
        CRUD::addColumn(['name' => 'state', 'label' => 'Status']);

        // Custom column to display motorbike reg_no
        CRUD::addColumn([
            'name' => 'items', // assuming RentingBookingItem has a motorbike relationship
            'label' => 'Motorbike Reg No',
            'type' => 'relationship',
            'attribute' => 'motorbike.reg_no',
        ]);

        // Display weekly rent and end date from the related RentingBookingItem
        CRUD::addColumn([
            'name' => 'items.weekly_rent',
            'label' => 'Weekly Rent',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'items.end_date',
            'label' => 'End Date',
            'type' => 'date',
        ]);

        // CRUD::addColumn([
        //     'name' => 'items', // assuming items is the relation defined in RentingBooking
        //     'label' => 'Weekly Rent',
        //     'type' => 'relationship',
        //     'attribute' => 'weekly_rent', // Assuming you're displaying one item, customize as necessary
        //     'model' => 'App\Models\RentingBookingItem' // Ensure to specify the model
        // ]);

        // CRUD::addColumn([
        //     'name' => 'items', // using the same relation
        //     'label' => 'End Date',
        //     'type' => 'relationship',
        //     'attribute' => 'end_date', // Customize as necessary
        //     'model' => 'App\Models\RentingBookingItem' // Specify the model
        // ]);

        CRUD::addColumn(['name' => 'customer.phone', 'label' => 'Customer Phone']);
        CRUD::addColumn(['name' => 'customer.email', 'label' => 'Customer Email']);

        // Filters
        CRUD::addFilter([
            'name' => 'customer_id',
            'type' => 'select2',
            'label' => 'Customer',
        ], function () {
            return Customer::all()->pluck('full_name', 'id')->toArray();
        });

        CRUD::addFilter([
            'name' => 'motorbike_id',
            'type' => 'select2',
            'label' => 'Motorbike',
        ], function () {
            return Motorbike::all()->pluck('reg_no', 'id')->toArray();
        });

        CRUD::addFilter([
            'name' => 'state',
            'type' => 'dropdown',
            'label' => 'Booking State',
        ], [
            'Completed & Issued' => 'Completed & Issued',
            'Completed' => 'Completed',
            'DRAFT' => 'DRAFT',
            'Awaiting Documents & Payment' => 'Awaiting Documents & Payment',
        ]);

        CRUD::addFilter([
            'name' => 'start_date',
            'type' => 'date_range',
            'label' => 'Date Range',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(NgnRentingBookingRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
