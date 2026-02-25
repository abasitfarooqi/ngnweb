<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PurchaseUsedVehicleRequest;
use App\Mail\PurchaseInvoiceReview;
use App\Models\PurchaseAgreementAccess;
use App\Models\PurchaseUsedVehicle;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PurchaseUsedVehicleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\PurchaseUsedVehicle::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/used-vehicle-seller');
        CRUD::setEntityNameStrings('Vehicle Purchase', 'Vehicle Purchases');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();

        CRUD::enableDetailsRow();

        // Conditionally allow details row per row

    }

    /**
     * Decide per-row whether to show the + button.
     */
    protected function showDetailsRowButton($entry)
    {
        // Check if the purchase email matches a customer
        return \App\Models\Customer::where('email', $entry->email)->exists();
    }

    public function showDetailsRow($id)
    {
        $purchase = PurchaseUsedVehicle::findOrFail($id);

        // Try to find the customer by email
        $customer = \App\Models\Customer::where('email', $purchase->email)->first();

        if (! $customer) {
            return '<div class="p-3 text-danger">No matching customer found for this email.</div>';
        }

        // Reuse CustomerCrudController’s details row logic
        return app(\App\Http\Controllers\Admin\CustomerCrudController::class)
            ->showDetailsRow($customer->id);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PurchaseUsedVehicleRequest::class);
        // CRUD::setFromDb();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('purchase_date')
            ->type('datetime')
            ->wrapper(['class' => 'form-group col-md-2'])
            ->value(now());

        CRUD::field('full_name')
            ->wrapper(['class' => 'form-group col-md-2'])
            ->label('Full Name');

        CRUD::field('address')
            ->wrapper(['class' => 'form-group col-md-2']);

        CRUD::field('postcode')
            ->wrapper(['class' => 'form-group col-md-2']);

        CRUD::field('phone_number')
            ->wrapper(['class' => 'form-group col-md-2']);

        CRUD::field('email')
            ->wrapper(['class' => 'form-group col-md-2']);

        // SECOND LINE
        CRUD::field('reg_no')
            ->wrapper(['class' => 'form-group col-md-1']);

        CRUD::field('make')
            ->wrapper(['class' => 'form-group col-md-1']);

        CRUD::field('model')
            ->wrapper(['class' => 'form-group col-md-2']);

        CRUD::field('year')
            ->wrapper(['class' => 'form-group col-md-1']);

        CRUD::field('colour')
            ->wrapper(['class' => 'form-group col-md-1']);

        CRUD::field('fuel_type')
            ->wrapper(['class' => 'form-group col-md-1'])
            ->default('Petrol');

        CRUD::field('current_mileage')
            ->wrapper(['class' => 'form-group col-md-1']);

        CRUD::field('vin')
            ->wrapper(['class' => 'form-group col-md-2']);

        CRUD::field('engine_number')
            ->wrapper(['class' => 'form-group col-md-2']);

        // THIRD LINE

        CRUD::field('price')
            ->wrapper(['class' => 'form-group col-md-3'])
            ->label('Vehicle Value')
            ->default(0.00);

        CRUD::field('deposit')
            ->label('Paid')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('outstanding')
            ->label('Outstanding')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('total_to_pay')
            ->wrapper(['class' => 'form-group col-md-3']);

        // FOURTH LINE

        CRUD::field('account_name')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('account_number')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('sort_code')
            ->wrapper(['class' => 'form-group col-md-4']);

        // FIFTH LINE
        // create virtual checkbox say send_email
        CRUD::field('send_email')
            ->type('checkbox')
            ->label('Send Email')
            ->wrapper(['class' => 'form-group col-md-2']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function postCreate($model)
    {
        $passcode = Str::random(12);
        $expiresAt = now()->addDays(1);

        $access = PurchaseAgreementAccess::create([
            'purchase_id' => $model->id,
            'passcode' => $passcode,
            'expires_at' => $expiresAt,
        ]);

        $url = route('purchase.invoice.show', ['passcode' => $passcode, 'purchase_id' => $model->id]);

        if ($access) {

            $qrBase64 = '';

            if (request()->input('send_email') && ! empty($model->email)) {
                $data['email'] = [$model->email, 'customerservice@neguinhomotors.co.uk'];

                $data['title'] = 'Vehicle Purchase Invoice Review';
                $data['body'] = 'Dear valued customer,

                                We kindly request your attention to finalise your invoice with Neguinho Motors. To proceed, please click the following link to review and provide your account details:';

                $mailData = [
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'url' => $url,
                ];

                Mail::to($data['email'])->send(new PurchaseInvoiceReview($mailData));
            } else {
                // Handle the case where no email is provided or send_email is not checked
                \Log::info('Email not sent: either no email provided or send_email not checked.');
            }

            return response()->json([
                'qrImage' => $qrBase64,
                'url' => $url,
            ]);
        }
    }

    private function filterEmails($emails)
    {
        if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(ngm|ngn)$/', $emails) || preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.ngn-$/', $emails)) {
            return true;
        } else {
            return false;
        }
    }
}
