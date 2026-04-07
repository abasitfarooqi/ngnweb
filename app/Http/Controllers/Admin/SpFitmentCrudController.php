<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpFitmentRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SpFitmentCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SpFitment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-fitment');
        CRUD::setEntityNameStrings('spare part fitment', 'spare part fitments');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('model_id')
            ->type('select')
            ->label('Model')
            ->entity('model')
            ->attribute('name')
            ->model(\App\Models\SpModel::class);
        CRUD::column('year')->type('text');
        CRUD::column('country_name')->type('text');
        CRUD::column('colour_name')->type('text');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('updated_at')->type('datetime');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpFitmentRequest::class);

        CRUD::field('model_id')
            ->type('select')
            ->label('Model')
            ->entity('model')
            ->attribute('name')
            ->model(\App\Models\SpModel::class);
        CRUD::field('year')->type('text');
        CRUD::field('country_slug')->type('text');
        CRUD::field('country_name')->type('text');
        CRUD::field('colour_slug')->type('text');
        CRUD::field('colour_name')->type('text');
        CRUD::field('is_active')->type('checkbox')->default(true);
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
