<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\CustomerDocument;
use App\Services\MotorbikeService;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// -- added thisX
use PhpParser\Node\Stmt\Label;

class CustomerCrudController extends BaseCrudController
{
    protected $motorbikeService;

    public function __construct(MotorbikeService $motorbikeService)
    {
        parent::__construct();
        $this->motorbikeService = $motorbikeService;
    }

    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Customer::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customer');
        CRUD::setEntityNameStrings('customer', 'customers');

    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::addButtonFromView('line', 'send_portal_credentials', 'send_portal_credentials', 'beginning');
        CRUD::addColumn(['name' => 'id', 'type' => 'number', 'label' => 'ID', 'width' => '80px']);

        CRUD::column('full_name')->label('Full Name');
        // CRUD::column('first_name'); // CRUD::column('last_name');
        CRUD::column('dob')->type('date');
        // CRUD::column('address'); // CRUD::column('postcode'); // CRUD::column('emergency_contact'); // CRUD::column('whatsapp');
        CRUD::column('phone');
        // CRUD::column('city'); // CRUD::column('country'); // CRUD::column('nationality');
        CRUD::column('email');
        CRUD::addColumn([
            'name' => 'is_register',
            'label' => 'Portal Active',
            'type' => 'boolean',
        ]);
        CRUD::column('reputation_note');
        // CRUD::column('age')->label('Age');
        CRUD::column('rating')->type('number')->label('Rating');

        CRUD::enableDetailsRow();

        $this->crud->addFilter(
            [
                'type' => 'text',
                'name' => 'full_name',
                'label' => 'Full Name',
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', \DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$value%");
            }
        );
    }

    public function showDetailsRow($id)
    {
        $customer = Customer::find($id);
        $customer_bookings = DB::select('
            SELECT
                rb.id as BID,
                m.id as MID,
                m.reg_no as REG_NO,
                rb.start_date as START_DATE,
                rb.state as STATE,
                rb.is_posted as IS_POSTED,
                rb.notes as NOTES,
                rb.deposit as DEPOSIT,
                rbi.weekly_rent as WEEKLY_RENT,
                rbi.is_posted as IS_POSTED,
                rbi.end_date as END_DATE,
                CASE
                    WHEN rb.start_date IS NOT NULL AND rbi.end_date IS NOT NULL THEN ROUND(DATEDIFF(rbi.due_date, rb.start_date) / 7.0)
                    WHEN rb.start_date IS NOT NULL AND rbi.end_date IS NULL THEN ROUND(DATEDIFF(NOW(), rb.start_date) / 7.0)
                    ELSE NULL
                END as WEEKS
            FROM renting_bookings rb
            INNER JOIN renting_booking_items rbi ON rbi.booking_id = rb.id
            INNER JOIN motorbikes m on m.id = rbi.motorbike_id
            WHERE rb.customer_id = :customer_id', ['customer_id' => $id]);

        $pcns_relevant_booking = collect($customer_bookings)->flatMap(function ($booking) use ($id) {
            return DB::table('pcn_cases')
                ->select('pcn_number as PCN_NO', 'date_of_contravention as PCN_DATE', 'time_of_contravention as PCN_TIME', 'isClosed as IS_CLOSED')
                ->where('date_of_contravention', '>=', $booking->START_DATE)
                ->where('date_of_contravention', '<=', $booking->END_DATE ?? now())
                ->where('customer_id', $id)
                ->where('motorbike_id', $booking->MID)
                ->orderBy('id')
                ->get();
        });

        $customer_contract = DB::table('finance_applications as fa')
            ->select('fa.id as CONTRACT_ID', 'fa.is_posted as IS_PROCEED', 'fa.contract_date as CONTRACT_DATE', 'fa.log_book_sent as LOGBOOK_SENT', 'm.reg_no as REG_NO')
            ->join('application_items as ai', 'ai.application_id', '=', 'fa.id')
            ->join('motorbikes as m', 'm.id', '=', 'ai.motorbike_id')
            ->where('fa.customer_id', $id)
            ->get();

        $pcns_relevant_contract = collect($customer_contract)->flatMap(function ($contract) use ($id) {

            $query = DB::table('pcn_cases as pc')
                ->select(
                    'pc.pcn_number as PCN_NO',
                    'pc.date_of_contravention as PCN_DATE',
                    'pc.time_of_contravention as PCN_TIME',
                    'pc.customer_id',
                    'pc.isClosed as IS_CLOSED',
                    'pc.motorbike_id as MID',
                    'm.reg_no as REG_NO'
                )
                ->join('motorbikes as m', 'm.id', '=', 'pc.motorbike_id')
                ->where('pc.customer_id', $id)
                ->distinct()
                ->orderBy('pc.id');

            if (! is_null($contract->CONTRACT_DATE)) {
                $query->where('pc.date_of_contravention', '>=', $contract->CONTRACT_DATE);
            }

            return $query->get();
        })->unique('PCN_NO');

        // Document Sections
        // contracts
        $customer_contracts = DB::table('customer_contracts as cc')
            ->select('cc.id as DOC_ID', 'cc.application_id as CONTRACT_ID', 'cc.file_name as FILE_NAME', 'cc.file_path as FILE_PATH', 'cc.sent_private as SENT_PRIVATE', 'cc.created_at as CREATED_AT', 'cc.updated_at as UPDATED_AT')
            ->where('cc.customer_id', $id)->get();

        // agreements
        $customer_agreements = DB::table('customer_agreements as ca')
            ->select('ca.id as DOC_ID', 'ca.booking_id as CONTRACT_ID', 'ca.file_name as FILE_NAME', 'ca.file_path as FILE_PATH', 'ca.sent_private as SENT_PRIVATE', 'ca.created_at as CREATED_AT', 'ca.updated_at as UPDATED_AT')
            ->where('ca.customer_id', $id)
            ->where('ca.is_verified', 1)
            ->get();

        // documents
        //         select cd.id as ID, cd.customer_id as CUSTOMER_ID,
        // 	cd.document_type_id as DOC_TYPE_ID, cd.file_name as FILE_NAME,
        // 	cd.file_path as FILE_PATH, cd.created_at as CREATED_AT, cd.updated_at as UPDATED_AT, cd.booking_id BOOKING_ID, cd.motorbike_id as MOTORBIKE_ID,
        // 	dt.name
        // 	from customer_documents cd
        // inner join document_types dt on dt.id  = cd.document_type_id
        //  ->where('ca.customer_id', $id)
        //  ->where('ca.is_verified', 1)
        //  ->get();

        $customer_documents = DB::table('customer_documents as cd')
            ->select('cd.id as ID', 'cd.customer_id as CUSTOMER_ID', 'cd.document_type_id as DOC_TYPE_ID', 'cd.file_name as FILE_NAME', 'cd.file_path as FILE_PATH', 'cd.sent_private as SENT_PRIVATE', 'cd.created_at as CREATED_AT', 'cd.updated_at as UPDATED_AT', 'cd.booking_id as BOOKING_ID', 'cd.motorbike_id as MOTORBIKE_ID', 'dt.name')
            ->join('document_types as dt', 'dt.id', '=', 'cd.document_type_id')
            ->where('cd.customer_id', $id)
            ->get();

        return view('vendor.backpack.crud.details_row', [
            'customer_bookings' => $customer_bookings,
            'customer' => $customer,
            'pcns_relevant_booking' => $pcns_relevant_booking,
            'customer_contract' => $customer_contract,
            'pcns_relevant_contract' => $pcns_relevant_contract,
            'customer_contracts' => $customer_contracts,
            'customer_agreements' => $customer_agreements,
            'customer_documents' => $customer_documents,
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CustomerRequest::class);
        CRUD::field('first_name')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('last_name')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('dob')->type('date')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('license_number')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('license_issuance_date')->type('date')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('license_expiry_date')->type('date')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('license_issuance_authority')->wrapper(['class' => 'form-group col-sm-12 col-md-2'])->label('License Issuance Country')->hint('Please select the country where the license was issued.');
        CRUD::field('nationality')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('city')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('country')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('postcode')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('address')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('emergency_contact')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('whatsapp')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('phone')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('email')->wrapper(['class' => 'form-group col-sm-12 col-md-2']);
        CRUD::field('reputation_note')->label('Note')->hint('Anything worth to note for that customer. FYI, that is not appear to customer at any place. (Internal only)');
        CRUD::field('rating')->type('number')->attributes(['step' => '1', 'min' => '1', 'max' => '5'])->label('Star Rating')->wrapper(['class' => 'form-group col-md-1']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        $entry = $this->crud->getCurrentEntry();
        $isActive = (bool) ($entry->is_register ?? false);
        CRUD::addField([
            'name' => 'portal_status_info',
            'type' => 'custom_html',
            'value' => '<div class="alert '.($isActive ? 'alert-success' : 'alert-warning').' mb-3">Portal is '
                .($isActive ? 'active' : 'not active')
                .' for this customer.</div>',
        ])->beforeField('first_name');
    }

    public function storeInline()
    {
        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();
        $item = $this->crud->create($request->all());

        return response()->json([
            'data' => $item,
            'redirect_url' => $this->crud->route,
            'toast' => [
                'type' => 'success',
                'title' => trans('backpack::crud.insert_success'),
                'body' => trans('backpack::crud.insert_success_message'),
            ],
        ]);
    }

    public function freenotify()
    {
        return view('livewire.agreements.migrated.taxnotification');
    }

    // -- added thisX Updated this
    public function notifyMotTax(Request $request)
    {

        $vehicleData = $this->motorbikeService->checkVehiclex($request->input('reg_no'));

        if ($vehicleData) {
            $data = $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'reg_no' => 'required',
                'phone' => 'required',
                'notify_email' => 'nullable',
                'notify_phone' => 'nullable',
                'enable' => 'nullable',
            ]);

            $data['notify_email'] = $request->has('notify_email');
            $data['notify_phone'] = $request->has('notify_phone');
            $data['enable'] = $request->has('enable');

            $vehicleNotification = new \App\Models\VehicleNotification;
            $vehicleNotification->fill($data);
            $vehicleNotification->save();

            return redirect()->route('notify.mottax.form')->with('success', 'Notification submitted successfully.');
        } else {
            return redirect()->route('notify.mottax.form')->with('error', 'Invalid registration number.');
        }
    }

    public function destroyContract($id)
    {
        $result = CustomerContract::deleteContractFile($id);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Contract moved to private.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Contract could not be moved.',
        ]);
    }

    public function deleteAgreement($id)
    {
        $agreement = CustomerAgreement::find($id);

        if (! $agreement || empty($agreement->file_path)) {
            Log::warning("No agreement file found for ID {$id}");

            return response()->json([
                'success' => false,
                'message' => 'Agreement file not found.',
            ]);
        }

        // Normalize stored path (remove leading slashes)
        $sourcePath = ltrim($agreement->file_path, '/');

        $diskPublic = Storage::disk('public');
        $diskPrivate = Storage::disk('private');
        $diskLocal = Storage::disk('local'); // storage/app

        $fromDisk = null;
        $diskName = null;

        // Detect which disk currently holds the file
        if ($diskPublic->exists($sourcePath)) {
            $fromDisk = $diskPublic;
            $diskName = 'public';
        } elseif ($diskLocal->exists($sourcePath)) {
            $fromDisk = $diskLocal;
            $diskName = 'local';
        } elseif ($diskPrivate->exists($sourcePath)) {
            // Already in private
            Log::info("Agreement file already in private: {$sourcePath}");
            $agreement->sent_private = true;
            $agreement->save();

            return response()->json([
                'success' => true,
                'message' => 'Agreement already private.',
            ]);
        } else {
            Log::warning("Agreement file not found in any disk: {$sourcePath}");

            return response()->json([
                'success' => false,
                'message' => 'Agreement file not found.',
            ]);
        }

        try {
            // Ensure destination directory exists
            $diskPrivate->makeDirectory(dirname($sourcePath));

            // Move file from detected disk to private
            $diskPrivate->put($sourcePath, $fromDisk->get($sourcePath));
            $fromDisk->delete($sourcePath);

            Log::info("Moved agreement file from {$diskName} to private: {$sourcePath}");

            // Update DB flag
            $agreement->sent_private = true;
            $agreement->save();

            Log::info("Agreement ID {$id} marked as private.");

            return response()->json([
                'success' => true,
                'message' => 'Agreement moved to private.',
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed moving agreement file {$sourcePath}: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => 'Failed to move agreement file.',
            ]);
        }
    }

    public function deleteDocument($id)
    {
        $document = CustomerDocument::find($id);

        if (! $document || empty($document->file_path)) {
            Log::warning("No document file found for ID {$id}");

            return response()->json([
                'success' => false,
                'message' => 'Document file not found.',
            ]);
        }

        $sourcePath = ltrim($document->file_path, '/'); // Normalize path (remove leading /)
        $diskPublic = Storage::disk('public');
        $diskPrivate = Storage::disk('private');
        $diskLocal = Storage::disk('local'); // storage/app

        $fromDisk = null;
        $diskName = null;

        // Check which disk the file exists on
        if ($diskPublic->exists($sourcePath)) {
            $fromDisk = $diskPublic;
            $diskName = 'public';
        } elseif ($diskLocal->exists($sourcePath)) {
            $fromDisk = $diskLocal;
            $diskName = 'local';
        } elseif ($diskPrivate->exists($sourcePath)) {
            // Already private, just mark it
            Log::info("Document file already in private: {$sourcePath}");
            $document->sent_private = true;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document already private.',
            ]);
        } else {
            Log::warning("Document file not found in any disk: {$sourcePath}");

            return response()->json([
                'success' => false,
                'message' => 'Document file not found in storage disks.',
            ]);
        }

        try {
            // Ensure destination directory exists in private
            $diskPrivate->makeDirectory(dirname($sourcePath));

            // Copy file contents and remove original
            $diskPrivate->put($sourcePath, $fromDisk->get($sourcePath));
            $fromDisk->delete($sourcePath);

            Log::info("Moved document file from {$diskName} to private: {$sourcePath}");

            // Mark document as private in DB
            $document->sent_private = true;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document moved to private.',
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed moving document file {$sourcePath}: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => 'Failed to move document file.',
            ]);
        }
    }
}
