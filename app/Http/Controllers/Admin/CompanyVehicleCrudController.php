<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CompanyVehicleRequest;
use App\Models\Motorbike;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CompanyVehicleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\CompanyVehicle::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/company-vehicle');
        CRUD::setEntityNameStrings('Personal Vehicle', 'Personal Vehicles');

    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
        CRUD::enableExportButtons();
        CRUD::column('motorbike.reg_no')->label('VRN');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CompanyVehicleRequest::class);
        CRUD::setFromDb();

        CRUD::field('motorbike_id')->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('reg_no')->model(Motorbike::class);

        CRUD::field('custodian')->label('Keeper')->hint('Who is user of this vehicle? (i.e., Thiago, Tooting, Catford, William, etc...!');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
