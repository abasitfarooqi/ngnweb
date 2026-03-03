<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RecoveredMotorbikeRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;

class RecoveredMotorbikeCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\RecoveredMotorbike::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/recovered-motorbike');
        CRUD::setEntityNameStrings('recovered motorbike', 'recovered motorbikes');
    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();

        CRUD::column('motorbike.reg_no')->label('VRM');

        CRUD::column('case_date')->label('DATE');

        CRUD::column('returned_date')->label('RETURNED DATE');

        CRUD::column('notes')->label('NOTES');

        CRUD::column('branch.name')->label('BRANCH');

        CRUD::removeColumn('motorbike_id');

        CRUD::removeColumn('branch_id');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(RecoveredMotorbikeRequest::class);
        CRUD::setFromDb();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::addField([
            'name' => 'case_date',
            'type' => 'datetime',
            'label' => 'Case Date',
            'attributes' => [
                'required' => 'required|datetime',
            ],
            'value' => now(),
        ]);

        CRUD::addField([
            'name' => 'returned_date',
            'type' => 'datetime',
            'label' => 'Returned Date',
            'attributes' => [
                'nullable' => true,
            ],
        ]);

        CRUD::addField([
            'name' => 'motorbike_id',
            'type' => 'select2',
            'label' => 'Motorbike',
            'entity' => 'motorbike',
            'attribute' => 'reg_no',
            'model' => \App\Models\Motorbike::class,
            'attributes' => [
                'required' => 'required',
            ],

        ]);

        CRUD::addField([
            'name' => 'branch_id',
            'type' => 'select2',
            'label' => 'Branch',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => \App\Models\Branch::class,
            'attributes' => [
                'required' => 'required',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function post_precheck($recoveredMotorbike)
    {

        $motorbike_id = $recoveredMotorbike->motorbike_id;

        $vehicle_issuance = DB::table('vehicle_issuances')
            ->where('motorbike_id', $motorbike_id)
            ->where('is_returned', false)
            ->first();

        if ($vehicle_issuance) {
            $vehicle_issuance_id = $vehicle_issuance->id;
            $vehicle_issuance = \App\Models\VehicleIssuance::find($vehicle_issuance_id);
            $vehicle_issuance->is_returned = true;
            $vehicle_issuance->save();
        }
    }
}
