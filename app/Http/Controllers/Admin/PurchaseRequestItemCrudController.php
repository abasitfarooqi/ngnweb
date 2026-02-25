<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PurchaseRequestItemRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PurchaseRequestItemCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\PurchaseRequestItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/purchase-request-item');
        CRUD::setEntityNameStrings('purchase request item', 'purchase request items');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
        CRUD::enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PurchaseRequestItemRequest::class);
        CRUD::setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        $this->crud->setOperationSetting('showDeleteButton', true);
    }
}
