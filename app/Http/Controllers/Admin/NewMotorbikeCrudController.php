<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NewMotorbikeRequest;
use App\Models\Branch;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class NewMotorbikeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\NewMotorbike::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/new-motorbike');
        CRUD::setEntityNameStrings('new motorbike', 'new motorbikes');
    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::column('branch.name')->label('BRANCH');

        CRUD::column('vrm')->label('VRM');

        CRUD::column('engine')->label('ENGINE');

        CRUD::column('colour')->label('COLOUR');

        CRUD::column('make')->label('MAKE');

        CRUD::column('model')->label('MODEL');

        CRUD::column('year')->label('YEAR');

        CRUD::column('vim')->label('VIM');

        CRUD::column('branch_id')->type('hidden')->remove();

        CRUD::column('is_vrm')->label('VRM AVAILABLE?')->type('boolean');

        CRUD::column('is_migrated')->label('ALLOCATED')->type('boolean');

        CRUD::column('status')->label('STATUS');

        CRUD::column('user_id')->remove();

        CRUD::column('user.first_name')->label('STAFF');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(NewMotorbikeRequest::class);
        CRUD::setFromDb();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::addField([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select2',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => Branch::class,
        ]);

        CRUD::field('is_vrm')->label('VRM AVAILABLE?')->type('checkbox')->hint('if VRM is available, check this box, else unchecked it and fill the VRM field with n/a or leave it empty');

        CRUD::field('is_migrated')->label('ALLOCATED?')->type('checkbox')->hint('if Bike is arrived new and have no customer to allocate.');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
