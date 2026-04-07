<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpStockMovementRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SpStockMovementCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\SpStockMovement::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sp-stock-movement');
        CRUD::setEntityNameStrings('spare part stock movement', 'spare part stock movements');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('sp_part_id')
            ->type('select')
            ->label('Part')
            ->entity('part')
            ->attribute('part_number')
            ->model(\App\Models\SpPart::class);
        CRUD::column('branch_id')
            ->type('select')
            ->label('Branch')
            ->entity('branch')
            ->attribute('name')
            ->model(\App\Models\Branch::class);
        CRUD::column('transaction_date')->type('datetime');
        CRUD::column('in')->type('number')->decimals(2);
        CRUD::column('out')->type('number')->decimals(2);
        CRUD::column('transaction_type')->type('text');
        CRUD::column('user_id')
            ->type('select')
            ->label('User')
            ->entity('user')
            ->attribute('name')
            ->model(\App\Models\User::class);
        CRUD::column('ref_doc_no')->type('text');
        CRUD::column('remarks')->type('text');
        CRUD::column('created_at')->type('datetime');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SpStockMovementRequest::class);

        CRUD::field('sp_part_id')
            ->type('select')
            ->label('Part')
            ->entity('part')
            ->attribute('part_number')
            ->model(\App\Models\SpPart::class);
        CRUD::field('branch_id')
            ->type('select')
            ->label('Branch')
            ->entity('branch')
            ->attribute('name')
            ->model(\App\Models\Branch::class);
        CRUD::field('transaction_date')
            ->type('datetime_picker')
            ->default(now()->format('Y-m-d H:i:s'));
        CRUD::field('in')->type('number')->attributes(['step' => '0.01'])->default(0);
        CRUD::field('out')->type('number')->attributes(['step' => '0.01'])->default(0);
        CRUD::field('transaction_type')
            ->type('select_from_array')
            ->options([
                'adjustment' => 'Adjustment',
                'transfer' => 'Transfer',
                'supplier' => 'Supplier',
                'sales' => 'Sales',
            ])
            ->default('adjustment');
        CRUD::field('user_id')
            ->type('select')
            ->label('User')
            ->entity('user')
            ->attribute('name')
            ->model(\App\Models\User::class)
            ->default(backpack_user()?->id);
        CRUD::field('ref_doc_no')->type('text');
        CRUD::field('remarks')->type('textarea');
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
