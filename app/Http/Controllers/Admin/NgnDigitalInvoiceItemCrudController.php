<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NgnDigitalInvoiceItemCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnDigitalInvoiceItemCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\NgnDigitalInvoiceItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-digital-invoice-item');
        CRUD::setEntityNameStrings('invoice item', 'invoice items');
    }

    protected function setupListOperation()
    {
        CRUD::column('item_name')->label('Item Name');
        CRUD::column('sku')->label('SKU');
        CRUD::column('quantity')->type('number');
        CRUD::column('price')->type('number')->decimals(2)->prefix('£')->label('Unit Price');
        CRUD::column('discount')->type('number')->decimals(2)->prefix('£');
        CRUD::column('tax')->type('number')->decimals(2)->prefix('£');
        CRUD::column('total')->type('number')->decimals(2)->prefix('£');
        CRUD::column('notes')->label('Notes');
    }

    protected function setupCreateOperation()
    {
        CRUD::field('item_name')->type('text')->label('Item Name')->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('sku')->type('text')->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('quantity')->type('number')->default(1)->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('price')->type('number')->attributes(['step' => '0.01'])->label('Unit Price')->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('discount')->type('number')->attributes(['step' => '0.01'])->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('tax')->type('number')->attributes(['step' => '0.01'])->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('notes')->type('textarea')->wrapper(['class' => 'form-group col-md-12']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
