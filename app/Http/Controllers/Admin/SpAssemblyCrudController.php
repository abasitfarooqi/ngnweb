<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpAssemblyRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SpAssemblyCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SpAssembly::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-assembly');
        CRUD::setEntityNameStrings('spare part assembly', 'spare part assemblies');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('fitment_id')
            ->type('select')
            ->label('Fitment')
            ->entity('fitment')
            ->attribute('admin_label')
            ->model(\App\Models\SpFitment::class);
        CRUD::column('slug')->type('text');
        CRUD::column('name')->type('text');
        CRUD::column('sort_order')->type('number');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('updated_at')->type('datetime');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpAssemblyRequest::class);

        CRUD::field('fitment_id')
            ->type('select')
            ->label('Fitment')
            ->entity('fitment')
            ->attribute('admin_label')
            ->model(\App\Models\SpFitment::class);
        CRUD::field('external_id')->type('text');
        CRUD::field('slug')->type('text');
        CRUD::field('name')->type('text');
        CRUD::field('image_url')->type('text');
        CRUD::field('diagram_url')->type('text');
        CRUD::field('sort_order')->type('number')->default(0);
        CRUD::field('is_active')->type('checkbox')->default(true);
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
