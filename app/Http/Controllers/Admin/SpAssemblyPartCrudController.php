<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpAssemblyPartRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SpAssemblyPartCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SpAssemblyPart::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-assembly-part');
        CRUD::setEntityNameStrings('assembly line item', 'assembly line items');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('assembly_id')
            ->type('select')
            ->label('Assembly')
            ->entity('assembly')
            ->attribute('name')
            ->model(\App\Models\SpAssembly::class);
        CRUD::column('part_id')
            ->type('select')
            ->label('Part')
            ->entity('part')
            ->attribute('part_number')
            ->model(\App\Models\SpPart::class);
        CRUD::column('qty_used')->type('number');
        CRUD::column('sort_order')->type('number');
        CRUD::column('updated_at')->type('datetime');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpAssemblyPartRequest::class);

        CRUD::field('assembly_id')
            ->type('select')
            ->label('Assembly')
            ->entity('assembly')
            ->attribute('name')
            ->model(\App\Models\SpAssembly::class);
        CRUD::field('part_id')
            ->type('select')
            ->label('Part')
            ->entity('part')
            ->attribute('part_number')
            ->model(\App\Models\SpPart::class);
        CRUD::field('qty_used')->type('number')->default(1);
        CRUD::field('sort_order')->type('number')->default(0);
        CRUD::field('note_override')->type('textarea');
        CRUD::field('price_override')->type('number')->attributes(['step' => '0.01']);
        CRUD::field('stock_override')->type('text');
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
