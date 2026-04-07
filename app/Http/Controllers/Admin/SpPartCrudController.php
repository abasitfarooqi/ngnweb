<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpPartRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SpPartCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SpPart::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-part');
        CRUD::setEntityNameStrings('spare part', 'spare parts');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('part_number')->type('text');
        CRUD::column('name')->type('text');
        CRUD::column('stock_status')->type('text');
        CRUD::column('price_gbp_inc_vat')->type('number')->decimals(2)->label('Price (GBP inc VAT)');
        CRUD::column('global_stock')->type('number')->decimals(2)->label('Global qty (from movements)');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('updated_at')->type('datetime');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpPartRequest::class);

        CRUD::field('part_number')->type('text');
        CRUD::field('name')->type('text');
        CRUD::field('note')->type('textarea');
        CRUD::field('stock_status')->type('text')->default('NOT IN STOCK');
        CRUD::field('price_gbp_inc_vat')->type('number')->attributes(['step' => '0.01']);
        CRUD::field('is_active')->type('checkbox')->default(true);
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(SpPartRequest::class);

        CRUD::field('part_number')->type('text');
        CRUD::field('name')->type('text');
        CRUD::field('note')->type('textarea');
        CRUD::field('stock_status')->type('text');
        CRUD::field('price_gbp_inc_vat')->type('number')->attributes(['step' => '0.01']);
        CRUD::field('global_stock')->type('number')->attributes(['step' => '0.01', 'readonly' => 'readonly'])
            ->hint('Adjusted via spare part stock handler or movement lines.');
        CRUD::field('is_active')->type('checkbox');
    }
}
