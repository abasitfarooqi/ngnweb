<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ClubMemberVehicleDetailsCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClubMemberVehicleDetailsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ClubMember::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/clubmembers-details');
        CRUD::setEntityNameStrings('Club Member Vehicles Details', 'Club Member Vehicles Details');
    }

    protected function setupListOperation()
    {
        // ✅ Filters
        CRUD::addFilter(
            [
                'name' => 'vrm',
                'type' => 'text',
                'label' => 'VRM',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'vrm', 'LIKE', "%$value%");
            }
        );

        CRUD::addFilter(
            [
                'name' => 'phone',
                'type' => 'text',
                'label' => 'Phone',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'phone', 'LIKE', "%$value%");
            }
        );

        CRUD::addFilter(
            [
                'name' => 'email',
                'type' => 'text',
                'label' => 'Email',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'email', 'LIKE', "%$value%");
            }
        );

        // ✅ Columns
        CRUD::column('full_name')->label('Full Name');
        // CRUD::column('email')->label('Email');
        // CRUD::column('phone')->label('Phone');
        CRUD::column('vrm')->label('VRM');
        CRUD::column('make')->label('Make');
        CRUD::column('model')->label('Model');
        CRUD::column('year')->label('Year');
    }

    protected function setupUpdateOperation()
    {
        CRUD::field('user_id')->label('User')->type('hidden');
        // ✅ Editable fields
        CRUD::field('vrm')->label('VRM')->type('text');
        CRUD::field('make')->label('Make')->type('text');
        CRUD::field('model')->label('Model')->type('text');
        CRUD::field('year')->label('Year')->type('number');

        // ✅ Read-only fields
        CRUD::field('full_name')->label('Full Name')->attributes(['readonly' => 'readonly']);
        CRUD::field('email')->label('Email')->attributes(['readonly' => 'readonly']);
        CRUD::field('phone')->label('Phone')->attributes(['readonly' => 'readonly']);
    }
}
