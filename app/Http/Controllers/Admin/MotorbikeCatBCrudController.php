<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MotorbikeCatBRequest;
use App\Models\Branch;
use App\Models\Motorbike;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class MotorbikeCatBCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\MotorbikeCatB::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/motorbike-cat-b');
        CRUD::setEntityNameStrings('Catagory-B Motorbike', 'Catagory-B Motorbikes');

    }

    protected function setupListOperation()
    {
        CRUD::column('motorbike.reg_no')->label('Registraction No');

        CRUD::setFromDb();
        CRUD::enableExportButtons();

        CRUD::addFilter(
            [
                'name' => 'motorbike_reg_no',
                'type' => 'text',
                'label' => 'Registration No',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'motorbike', function ($query) use ($value) {
                    $query->where('reg_no', 'LIKE', "%$value%");
                });
            }
        );

        CRUD::removeColumn('motorbike_id');
    }

    public function fetchMotorbike()
    {
        return $this->fetch([
            'model' => Motorbike::class,
            'query' => function ($model) {
                return $model->where('vehicle_profile_id', 1);
            },
            'searchable_attributes' => ['reg_no'],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MotorbikeCatBRequest::class);
        CRUD::setFromDb();

        CRUD::addField([
            'name' => 'motorbike_id',
            'label' => 'Motorbike',
            'type' => 'select2_from_ajax',
            'entity' => 'motorbike',
            'attribute' => 'reg_no',
            'model' => Motorbike::class,
            // 'ajax' => true,
            // 'inline_create' => true,
            // 'query' => function ($query) {
            //     return $query->where('vehicle_profile_id', 1);
            // }
        ]);

        CRUD::addField([
            'name' => 'motorbike_id',
            'label' => 'Motorbike',
            'type' => 'relationship',
            'entity' => 'motorbike',
            'attribute' => 'reg_no',
            'model' => Motorbike::class,
            // 'ajax' => true,
            'inline_create' => ['entity' => 'motorbike'],
        ]);

        CRUD::addField([
            'name' => 'dop',
            'label' => 'Date of Purchase',
            'type' => 'datetime',
            'default' => now(),
            'validation' => 'required|datetime',
        ]);

        CRUD::field('notes')->label('Vehicle Damage Description')->type('textarea')->hint('Any additional information, how much paid, from whom bought it, etc...!');

        CRUD::addField([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select2',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => Branch::class,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
