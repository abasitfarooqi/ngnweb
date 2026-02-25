<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VehicleIssuanceRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class VehicleIssuanceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\VehicleIssuance::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/vehicle-issuance');
        CRUD::setEntityNameStrings('vehicle issuance', 'vehicle issuances');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
        CRUD::enableExportButtons();
    }

    protected function setupCreateOperation()
    {

        CRUD::setValidation(VehicleIssuanceRequest::class);

        CRUD::setFromDb();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::addField([
            'name' => 'issue_date',
            'type' => 'datetime',
            'label' => 'Issued Date',
            'value' => now(),
        ]);

        CRUD::addField([
            'name' => 'motorbike_id',
            'type' => 'select2',
            'label' => 'Motorbike',
            'entity' => 'motorbike',
            'attribute' => 'reg_no',
            'model' => \App\Models\Motorbike::class,
            'attributes' => [
                'required' => 'required',
            ],
        ]);

        CRUD::addField([
            'name' => 'branch_id',
            'type' => 'select2',
            'label' => 'Branch',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => \App\Models\Branch::class,
            'attributes' => [
                'required' => 'required',
            ],
        ]);

        CRUD::addField([
            'name' => 'customer_id',
            'type' => 'select2',
            'label' => 'Customer',
            'entity' => 'customer',
            'attribute' => 'first_name',
            'model' => \App\Models\Customer::class,
            'attributes' => [
                'required' => 'required',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
