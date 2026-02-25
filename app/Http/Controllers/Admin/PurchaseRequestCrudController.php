<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PurchaseRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PurchaseRequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\PurchaseRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/purchase-request');
        CRUD::setEntityNameStrings('purchase request', 'purchase requests');

    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PurchaseRequestRequest::class);
        CRUD::setFromDb();

        CRUD::addField(['name' => 'created_by', 'type' => 'hidden', 'default' => backpack_user()->id]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        $this->crud->setOperationSetting('showDeleteButton', true);
    }
}
