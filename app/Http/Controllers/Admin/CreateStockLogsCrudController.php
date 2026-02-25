<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateStockLogsRequest;
use App\Models\Branch;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CreateStockLogsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\CreateStockLogs::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/create-stock-logs');
        CRUD::setEntityNameStrings('Stock Logs', 'Stock Logs');

        CRUD::field('picture')
            ->label('Picture')
            ->type('upload')
            ->withFiles([
                'disk' => 'public',
                'path' => 'uploads/stocks',
            ]);
    }

    public function setupListOperation()
    {
        CRUD::column('description')->type('text')->label('Description');

        CRUD::column('color')->type('text')->label('Color');

        CRUD::addColumn([
            'name' => 'picture',
            'label' => 'Picture',
            'type' => 'image',
            'prefix' => 'storage/uploads/stocks/',
            'width' => '80px',
        ]);

        CRUD::column('sku')->type('text')->label('SKU');

        CRUD::column('qty')->type('number')->label('Qty');

        CRUD::enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CreateStockLogsRequest::class);

        CRUD::setFromDb();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        // CRUD::addField([
        //     'name' => 'branch_id',
        //     'label' => 'Branch',
        //     'type' => 'select2',
        //     'entity' => 'branch',
        //     'attribute' => 'name',
        //     'model' => Branch::class,
        // ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
