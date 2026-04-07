<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpModelRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SpModelCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SpModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-model');
        CRUD::setEntityNameStrings('spare part model', 'spare part models');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('make_id')
            ->type('select')
            ->label('Make')
            ->entity('make')
            ->attribute('name')
            ->model(\App\Models\SpMake::class);
        CRUD::column('slug')->type('text');
        CRUD::column('name')->type('text');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('updated_at')->type('datetime');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpModelRequest::class);

        CRUD::field('make_id')
            ->type('select')
            ->label('Make')
            ->entity('make')
            ->attribute('name')
            ->model(\App\Models\SpMake::class);
        CRUD::field('slug')->type('text');
        CRUD::field('name')->type('text');
        CRUD::field('is_active')->type('checkbox')->default(true);
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
