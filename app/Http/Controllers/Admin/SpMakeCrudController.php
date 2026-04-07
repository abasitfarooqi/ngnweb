<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpMakeRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SpMakeCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SpMake::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-make');
        CRUD::setEntityNameStrings('spare part make', 'spare part makes');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('slug')->type('text');
        CRUD::column('name')->type('text');
        CRUD::column('source')->type('text');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('updated_at')->type('datetime');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpMakeRequest::class);

        CRUD::field('slug')->type('text')->hint('URL-safe identifier, unique.');
        CRUD::field('name')->type('text');
        CRUD::field('source')->type('text')->default('internal');
        CRUD::field('is_active')->type('checkbox')->default(true);
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
