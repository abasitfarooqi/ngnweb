<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BookingInvoiceRequest;
use App\Models\BookingInvoice;
use App\Models\RentingTransaction;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingInvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\BookingInvoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/booking-invoice');
        CRUD::setEntityNameStrings('booking invoice', 'booking invoices');

    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(BookingInvoiceRequest::class);

        CRUD::setFromDb();
        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::addField([
            'name' => 'booking_id',
            'type' => 'select2',
            'label' => 'Booking',
            'entity' => 'booking',
            'attribute' => 'id',
            'model' => "App\Models\RentingBooking",
        ]);

        CRUD::addField([
            'name' => 'invoice_date',
            'type' => 'date',
            'label' => 'Invoice Date',
            'default' => date('Y-m-d'),
        ]);

        CRUD::addField([
            'name' => 'amount',
            'type' => 'number',
            'hint' => 'Enter Weekly Rent Only',
            'label' => 'Amount',
        ]);

        CRUD::addField([
            'name' => 'deposit',
            'type' => 'number',
            'label' => 'Deposit',
            'instruction' => 'Enter the Deposit Amount',
            'hint' => 'Leave Zero if Customer already deposit against the booking id',
            'wrapperAttributes' => ['class' => 'form-group col-md-6'],
            'attributes' => [
                'placeholder' => 'Enter the Deposit Amount',
                'default' => '0.00',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_posted',
            'type' => 'checkbox',
            'default' => true,
            'hint' => 'Must Checked it to Proceed Invoice. If not checked, the invoice will not be visible in the payment section',
            'label' => 'Proceed Invoice',
        ]);

        // CRUD::addField([
        //     'name' => 'is_paid',
        //     'type' => 'hidden',
        //     'label' => 'Is Paid',
        //     'default' => 0,
        // ]);

        CRUD::addField([
            'name' => 'is_paid',
            'type' => 'checkbox',
            'hint' => 'Must Leave it unchecked',
            'label' => 'Is Paid',
            'default' => false,
        ]);

        CRUD::addField([
            'name' => 'paid_date',
            'type' => 'hidden',
            'default' => null,
            'label' => 'Paid Date',
        ]);

        CRUD::addField([
            'name' => 'state',
            'type' => 'hidden',
            'default' => 'Awaiting Payment',
        ]);

        CRUD::addField([
            'name' => 'notes',
            'type' => 'textarea',
            'label' => 'Notes',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function processPayment(Request $request)
    {
        $paymentAmount = $request->input('payment_amount');

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Retrieve pending invoices
            $pendingInvoices = BookingInvoice::where('status', 'pending')->orderBy('created_at')->get();

            foreach ($pendingInvoices as $invoice) {
                if ($paymentAmount >= $invoice->amount) {

                    $paymentAmount -= $invoice->amount;

                    $invoice->status = 'paid';
                    $invoice->save();

                    RentingTransaction::create([
                        'booking_invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                    ]);
                } else {
                    break;
                }

                if ($paymentAmount <= 0) {
                    break;
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Payment processed successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with('error', 'Error processing payment: '.$e->getMessage());
        }
    }
}
