<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PcnTolRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Class PcnTolRequestCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PcnTolRequestCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\PcnTolRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/pcn-tol-request');
        CRUD::setEntityNameStrings('pcn tol request', 'pcn tol requests');
    }

    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);
        CRUD::column('update_id')->label('PCN Update');
        CRUD::column('request_date');
        CRUD::column('status');
        CRUD::column('letter_sent_at');
        CRUD::column('note');
        CRUD::column('created_at');

        CRUD::addButtonFromModelFunction('line', 'generate_tol_pdf', 'generateTolPdfButton', 'beginning');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PcnTolRequestRequest::class);

        // 👇 Add hidden field for case ID
        // CRUD::field('pcn_case_id')->type('hidden')->value(request()->get('pcn_case_id'));

        if (request()->has('update_id')) {
            // If coming from a button/link, just store the hidden update_id
            CRUD::field('update_id')
                ->type('hidden')
                ->value(request('update_id'));
        } else {
            // Select2 dropdown showing PCN number + update ID + customer name
            CRUD::field('update_id')
                ->type('select2')
                ->entity('pcnCaseUpdate')
                ->attribute('display_for_tol') // <- matches accessor above
                ->model(\App\Models\PcnCaseUpdate::class)
                ->label('PCN Update');
        }

        CRUD::field('request_date')->type('date')->default(now());
        CRUD::field('status')->type('select_from_array')->options([
            'pending' => 'Pending',
            'sent' => 'Sent',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ]);
        CRUD::field('letter_sent_at')->type('datetime_picker');
        CRUD::field('note')->type('textarea');

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

    }

    public function store(Request $request)
    {
        $redirect_location = $this->traitStore();
        $tolRequest = $this->crud->entry;

        // Always resolve the case ID from update_id
        if ($tolRequest->update_id) {
            $tolRequest->pcn_case_id = $tolRequest->pcnCaseUpdate->pcn_case_id;
            $tolRequest->save();
        }

        // PDF generation as you already have...
        $pdf = Pdf::loadView('pcn.template.tol_letter', [
            'tolRequest' => $tolRequest, // pass the full request
            'pcnNumber' => $tolRequest->pcnCaseUpdate->pcnCase->pcn_number ?? '',
            'customerName' => optional($tolRequest->pcnCaseUpdate->pcnCase->customer)->full_name ?? '',
            'vehicleVrm' => $tolRequest->pcnCaseUpdate->pcnCase->motorbike->reg_no ?? '',
            'userName' => $tolRequest->user->full_name ?? '',
        ]);

        $fileName = 'tol_request_'.$tolRequest->id.'.pdf';
        $path = storage_path('app/public/tol_letters/'.$fileName);
        $pdf->save($path);

        $tolRequest->update([
            'full_path' => 'storage/tol_letters/'.$fileName,
        ]);

        session()->flash('tol_pdf_file', $fileName);

        return $redirect_location;
    }

    public function update(Request $request)
    {
        $redirect_location = $this->traitUpdate();
        $tolRequest = $this->crud->entry;

        // Again ensure case_id is always linked
        if ($tolRequest->update_id) {
            $tolRequest->pcn_case_id = $tolRequest->pcnCaseUpdate->pcn_case_id;
            $tolRequest->save();
        }

        return $redirect_location;
    }

    public function generateTolLetterPdf($id)
    {
        $tolRequest = \App\Models\PcnTolRequest::with(['pcnCase.customer', 'pcnCase.motorbike', 'user'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pcn.template.tol_letter', [
            'tolRequest' => $tolRequest,
            'pcnNumber' => $tolRequest->pcnCaseUpdate->pcnCase->pcn_number ?? '',
            'customerName' => optional($tolRequest->pcnCaseUpdate->pcnCase->customer)->full_name ?? '',
            'vehicleVrm' => $tolRequest->pcnCaseUpdate->pcnCase->motorbike->reg_no ?? '',
            'userName' => $tolRequest->user->full_name ?? '',
        ]);

        $fileName = 'tol_request_'.$tolRequest->id.'.pdf';

        return $pdf->download($fileName);
    }
}
