<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClaimMotorbikeRequest;
use App\Models\Branch;
use App\Models\Motorbike;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ClaimMotorbikeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\ClaimMotorbike::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/claim-motorbike');
        CRUD::setEntityNameStrings('claim motorbike', 'claim motorbikes');

    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();

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

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClaimMotorbikeRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('motorbike_id')->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('reg_no')->model(Motorbike::class);

        CRUD::addField([
            'name' => 'fullname',
            'label' => 'Full Name',
            'type' => 'text',

            'validation' => 'required|string',
        ]);

        CRUD::addField([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'text',

            'validation' => 'required|string',
        ]);

        CRUD::addField([
            'name' => 'phone',
            'label' => 'Phone',
            'type' => 'text',

            'validation' => 'required|string',
        ]);

        CRUD::addField([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select2',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => Branch::class,
        ]);

        CRUD::addField([
            'name' => 'case_date',
            'label' => 'Case Date',
            'type' => 'datetime',
            'hint' => 'When the case is reported.',
            'default' => now(),
            'validation' => 'required|datetime',
        ]);

        CRUD::addField([
            'name' => 'is_received',
            'label' => 'Received Motorbike',
            'hint' => 'Check if the motorbike is received with keys.',
            'type' => 'checkbox',
            'validation' => 'boolean',
        ]);

        CRUD::addField([
            'name' => 'received_date',
            'label' => 'Received Date',
            'hint' => 'When the motorbike is received in premises. Probably, we do not tell insurece company this date.',
            'type' => 'datetime',
            'default' => now(),
            'validation' => 'nullable|datetime',
        ]);

        CRUD::addField([
            'name' => 'is_returned',
            'label' => 'Returned Motorbike',
            'type' => 'checkbox',
            'hint' => 'Return to anyone, keeper or insurance company if they take it.',
            'validation' => 'boolean',
        ]);

        CRUD::addField([
            'name' => 'returned_date',
            'label' => 'Returned Date',
            'type' => 'datetime',
            'validation' => 'nullable|datetime',
        ]);

        CRUD::addField([
            'name' => 'notes',
            'label' => 'Vehicle Damage Description',
            'type' => 'textarea',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
