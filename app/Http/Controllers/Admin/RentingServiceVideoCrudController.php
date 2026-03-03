<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RentingServiceVideoRequest;
use App\Models\Customer;
use App\Models\RentingBooking;
use App\Models\RentingServiceVideo;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RentingServiceVideoCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RentingServiceVideoCrudController extends BaseCrudController
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
        CRUD::setModel(RentingServiceVideo::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/renting-service-video');
        CRUD::setEntityNameStrings('Renting Service Video', 'Renting Service Videos');

        // Enable create operation for uploading video against booking
        $this->crud->allowAccess(['list', 'show', 'create']);
        $this->crud->denyAccess(['update', 'delete']);
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
        CRUD::column('booking_id');
        CRUD::addColumn([
            'name' => 'customer_name',
            'label' => 'Customer Name',
            'type' => 'text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('rentingBooking.customer', function ($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%$searchTerm%")
                        ->orWhere('last_name', 'like', "%$searchTerm%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%$searchTerm%"]);
                });
            },
            'orderable' => false,
        ]);

        // CRUD::column('video_path');
        CRUD::column('recorded_at');
        CRUD::addColumn([
            'name' => 'details',
            'label' => 'Details',
            'type' => 'closure',
            'function' => function ($entry) {
                return '<a href="'.$entry->video_url.'" target="_blank">'.$entry->video_url.'</a>';
            },
            'escaped' => false,
        ]);

        // Filter by booking_id
        CRUD::addFilter([
            'name' => 'booking_id',
            'type' => 'text',
            'label' => 'Booking ID',
        ],
            false,
            function ($value) {
                CRUD::addClause('where', 'booking_id', $value);
            });

        // Filter by customer name
        CRUD::addFilter([
            'name' => 'customer_name',
            'type' => 'text',
            'label' => 'Customer Name',
        ],
            false,
            function ($value) {
                CRUD::addClause('whereHas', 'rentingBooking.customer', function ($q) use ($value) {
                    $q->where('first_name', 'like', "%$value%")
                        ->orWhere('last_name', 'like', "%$value%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%$value%"]);
                });
            });
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
        CRUD::setValidation(RentingServiceVideoRequest::class);

        // Use a select2 field for booking_id to ensure video is uploaded against a booking
        CRUD::addField([
            'name' => 'booking_id',
            'label' => 'Booking',
            'type' => 'select2',
            'entity' => 'rentingBooking',
            'attribute' => 'customer_name_and_booking_id', // use accessor for display
            'model' => RentingBooking::class,
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);
        // Optionally, you can uncomment and use a file upload field for video_path if needed
        CRUD::field('video_path')->type('upload')->label('Video File')->upload(true);

        CRUD::field('recorded_at')->type('datetime_picker')->label('Recorded At')->wrapper(['class' => 'form-group col-md-6']);
        // CRUD::addField([
        //     'name' => 'details',
        //     'label' => 'Details',
        //     'type' => 'textarea',
        // ]);
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
