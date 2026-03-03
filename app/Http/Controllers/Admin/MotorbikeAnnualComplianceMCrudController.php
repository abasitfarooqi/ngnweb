<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MotorbikeAnnualComplianceMRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class MotorbikeAnnualComplianceMCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\MotorbikeAnnualCompliance::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-annual-compliance-m');
        CRUD::setEntityNameStrings('Motorbike Tax/MOT Manually Insert', 'Motorbike Tax/MOT Manually Insert');

        $this->crud->denyAccess('search');
    }

    protected function setupListOperation()
    {
        CRUD::column('motorbike.reg_no')->label('VRM');
        CRUD::setFromDb();
        CRUD::enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MotorbikeAnnualComplianceMRequest::class);
        CRUD::setFromDb();

        CRUD::field('year')
            ->value('2025')
            ->type('hidden');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
