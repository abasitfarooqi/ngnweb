<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RentingPricingRequest;
use App\Models\Motorbike;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RentingPricingCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\RentingPricing::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/renting-pricing');
        CRUD::setEntityNameStrings('renting pricing', 'renting pricings');

    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::addColumn([
            'name' => 'motorbike.reg_no',
            'label' => 'VRM / REG No',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'weekly_price',
            'label' => 'Weekly Price',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'minimum_deposit',
            'label' => 'Minimum Deposit',
            'type' => 'number',
        ]);

        CRUD::addFilter(
            [
                'name' => 'motorbike_reg_no',
                'type' => 'text',
                'label' => 'VRM / REG No',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'motorbike', function ($query) use ($value) {
                    $query->where('reg_no', 'LIKE', "%$value%");
                });
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(RentingPricingRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        // ONLY UPDATES FOLLOWINGS
        // 'motorbike_id',
        // 'user_id',
        // 'iscurrent',
        // 'weekly_price',
        // 'update_date',
        // 'minimum_deposit',

        CRUD::field('motorbike_id')->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('reg_no')->model(Motorbike::class);

        CRUD::field('weekly_price')->label('Weekly Price');
        CRUD::field('minimum_deposit')->label('Minimum Deposit');
        CRUD::field('iscurrent')->label('Is Current')->type('hidden')->default(true);
        CRUD::field('update_date')->label('Last Update Date')->type('datetime')->default(date('Y-m-d H:i:s'));
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
