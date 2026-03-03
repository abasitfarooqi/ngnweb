<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PcnCaseUpdateRequest;
use App\Models\PcnCase;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PcnCaseUpdateCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\PcnCaseUpdate::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/pcn-case-update');
        CRUD::setEntityNameStrings('pcn case update', 'pcn case updates');

    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::column('case_id');
        CRUD::column('pcncase.pcn_number')->label('PCN Number');
        CRUD::column('pcncase.motorbike.reg_no')->label('VRN');
        CRUD::column('update_date');
        CRUD::column('is_appealed')
            ->label('APPEALED')
            ->type('check');
        CRUD::column('is_paid_by_owner')
            ->label('PAID BY OWNER/NGN')
            ->type('check');
        CRUD::column('is_paid_by_keeper')
            ->label('PAID BY USER/KEEPER')
            ->type('check');
        CRUD::column('is_cancled')
            ->label('CANCELLED')
            ->type('check');
        CRUD::column('is_transferred')
            ->label('TRANSFERRED')
            ->type('check');
        CRUD::column('additional_fee');
        CRUD::column('note');
        CRUD::column('pcncase.user.first_name')->label('Updated By');

        CRUD::addFilter(
            [
                'name' => 'pcn_number',
                'type' => 'text',
                'label' => 'PCN Number',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'pcncase', function ($query) use ($value) {
                    $query->where('pcn_number', 'LIKE', "%$value%");
                });
            }
        );
        CRUD::addFilter(
            [
                'name' => 'reg_no',
                'type' => 'text',
                'label' => 'Registration Number',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'pcncase.motorbike', function ($query) use ($value) {
                    $query->where('reg_no', 'LIKE', "%$value%");
                });
            }
        );

        CRUD::addButtonFromModelFunction('line', 'request_tol', 'requestTolButton', 'beginning');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PcnCaseUpdateRequest::class);
        CRUD::setFromDb();

        $caseId = request()->input('case_id');
        $lastPcnUpdate = null;
        if ($caseId) {
            $lastPcnUpdate = \App\Models\PcnCaseUpdate::where('case_id', $caseId)
                ->latest('created_at')
                ->first();
        }

        CRUD::field('update_date')
            ->label('Update Date & Time')
            ->type('datetime_picker')
            ->default(now()->toDateTimeString());

        CRUD::field('case_id')
            ->label('Pcn Case')
            ->type('select2')
            ->entity('pcncase')
            ->attribute('pcn_number')
            ->model(PcnCase::class);

        CRUD::addField(['name' => 'picture_url', 'type' => 'hidden']);

        CRUD::field('is_appealed')
            ->label('APPEALED')
            ->type('checkbox')
            ->default($lastPcnUpdate ? (bool) $lastPcnUpdate->is_appealed : false);

        CRUD::field('is_cancled')
            ->label('PCN Canceled')
            ->type('checkbox')
            ->default(false)
            ->hint('PCN Canceled by Govt/Concil. Could be result of Appeal or any other reason. By marked Checked, it will reverse PCN dues across the system.');

        CRUD::field('is_paid_by_owner')
            ->label('NGN PAID')
            ->type('checkbox')
            ->hint('PCN Paid by Neguinho Motors Ltd may subject to recovery from the user/keeper')
            ->default($lastPcnUpdate ? (bool) $lastPcnUpdate->is_paid_by_owner : false);

        CRUD::field('is_paid_by_keeper')
            ->label('PAID BY USER/KEEPER')
            ->type('checkbox')
            ->hint('PCN Paid by the user/keeper. By marked Checked, it will reverse PCN dues across the system.')
            ->default($lastPcnUpdate ? (bool) $lastPcnUpdate->is_paid_by_keeper : false);

        CRUD::field('is_transferred')
            ->label('TRANSFERRED')
            ->type('checkbox')
            // ->hint('PCN Transferred to another user/keeper. By marked Checked, it will reverse PCN dues across the system.')
            ->default($lastPcnUpdate ? (bool) $lastPcnUpdate->is_transferred : false);

        CRUD::field('is_cancled')
            ->label('CANCELLED')
            ->type('checkbox')
            ->hint('PCN Canceled by Govt/Concil. Could be result of Appeal or any other reason. By marked Checked, it will reverse PCN dues across the system.')
            ->default($lastPcnUpdate ? (bool) $lastPcnUpdate->is_cancled : false);

        CRUD::field('additional_fee')
            ->label('Additional Fee')
            ->hint('Additional fee paid by the user/keeper. i.e., Appeal fee, late fee, etc.')
            ->default($lastPcnUpdate ? $lastPcnUpdate->additional_fee : 0);

        CRUD::field('note')
            ->label('Note')
            ->hint('Additional information about the update. i.e., picture link, document link, other important information. note: all kind of picture/document should be uploaded in office365 and link should be provided here.')
            ->type('textarea');

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);
        // CRUD::addField([
        //     'name' => 'picture_url',
        //     'label' => 'Picture',
        //     'type' => 'upload',
        //     'upload' => true,
        //     'disk' => 'uploads',
        // ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function requestTolButton($crud = false)
    {
        $url = backpack_url('pcn-tol-request/create?update_id='.$this->id);

        return '<a class="btn btn-sm btn-link" href="'.$url.'"><i class="la la-file"></i> REQUEST TOL</a>';
    }
}
