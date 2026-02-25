<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\BookingInvoice;
use App\Models\RentingTransaction;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait MergeInvoiceOperation
{
    protected function setupMergeInvoiceRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/merge-invoice', [
            'as' => $routeName.'.mergeInvoice',
            'uses' => $controller.'@mergeInvoice',
            'operation' => 'mergeInvoice',
        ]);
        Route::post($segment.'/merge-invoice/process', [
            'as' => $routeName.'.mergeInvoice.process',
            'uses' => $controller.'@processMergeInvoice',
            'operation' => 'mergeInvoice',
        ]);
    }

    protected function setupMergeInvoiceDefaults()
    {
        CRUD::allowAccess('mergeInvoice');

        CRUD::operation('mergeInvoice', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            CRUD::addButton('top', 'merge_invoice', 'view', 'crud::buttons.merge_invoice');
            CRUD::addButton('line', 'merge_invoice', 'view', 'crud::buttons.merge_invoice');
        });
    }

    public function mergeInvoice()
    {
        CRUD::hasAccessOrFail('mergeInvoice');

        $pendingInvoices = BookingInvoice::where('is_paid', false)->get();
        $totalAmount = $pendingInvoices->sum('amount');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Merge Invoice '.$this->crud->entity_name;
        $this->data['pendingInvoices'] = $pendingInvoices;
        $this->data['totalAmount'] = $totalAmount;

        return view('crud::operations.merge_invoice', $this->data);
    }

    public function processMergeInvoice(Request $request)
    {
        CRUD::hasAccessOrFail('mergeInvoice');

        $paymentAmount = $request->input('payment_amount');
        $pendingInvoices = BookingInvoice::where('is_paid', false)->orderBy('invoice_date')->get();

        foreach ($pendingInvoices as $invoice) {
            if ($paymentAmount >= $invoice->amount) {
                $paymentAmount -= $invoice->amount;
                $invoice->is_paid = true;
                $invoice->paid_date = now();
                $invoice->save();

                RentingTransaction::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $invoice->amount,
                    'transaction_date' => now(),
                    'booking_id' => $invoice->booking_id, // Ensure booking_id is correctly referenced
                    'transaction_type_id' => 1, // Adjust as needed
                    'payment_method_id' => 1, // Adjust as needed
                    'user_id' => $invoice->user_id, // Ensure user_id is correctly referenced
                    'notes' => 'Invoice payment',
                ]);
            } else {
                break;
            }
        }

        return redirect()->back()->with('success', 'Invoices paid successfully');
    }
}
