<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnDigitalInvoiceRequest;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\NgnDigitalInvoice;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Request;

/**
 * Class NgnDigitalInvoiceCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnDigitalInvoiceCrudController extends CrudController
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

    public function setup()
    {
        CRUD::setModel(NgnDigitalInvoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-digital-invoice');
        CRUD::setEntityNameStrings('ngn digital invoice', 'ngn digital invoices');
    }

    protected function setupListOperation()
    {
        CRUD::column('invoice_number')->label('No.');
        CRUD::column('invoice_type')->label('Type');
        CRUD::column('invoice_category')->label('Category');
        CRUD::column('customer_name')->label('Customer');
        CRUD::column('issue_date')->type('date');
        CRUD::column('total')->type('number')->decimals(2)->prefix('£');
        CRUD::column('status')->type('badge')->options([
            'draft' => 'secondary', 'approved' => 'info', 'sent' => 'warning', 'paid' => 'success', 'cancelled' => 'danger',
        ]);

        // Filters
        CRUD::addFilter([
            'name' => 'invoice_type',
            'type' => 'dropdown',
            'label' => 'Type',
        ], [
            'repair' => 'Repair', 'rental' => 'Rental', 'sale' => 'Sale', 'service' => 'Service',
        ], fn ($v) => CRUD::addClause('where', 'invoice_type', $v));

        CRUD::addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status',
        ], [
            'draft' => 'Draft', 'approved' => 'Approved', 'sent' => 'Sent', 'paid' => 'Paid', 'cancelled' => 'Cancelled',
        ], fn ($v) => CRUD::addClause('where', 'status', $v));

        CRUD::addFilter([
            'name' => 'issue_date',
            'type' => 'date_range',
            'label' => 'Issue Date',
        ], false, function ($value) {
            $dates = json_decode($value, true);
            CRUD::addClause('where', 'issue_date', '>=', $dates['from']);
            CRUD::addClause('where', 'issue_date', '<=', $dates['to']);
        });

        CRUD::addClause('orderBy', 'issue_date', 'desc');
        CRUD::addClause('orderBy', 'total', 'asc');

        // Add a button to generate a PDF
        CRUD::addButtonFromModelFunction('line', 'print', 'printPdfButton', 'end');
    }

    // Add this method to the NgnDigitalInvoice model
    public function printPdfButton()
    {
        return '<a class="btn btn-sm btn-link" target="_blank" href="'.url('admin/bookings/invoices/'.$this->crud->getCurrentEntry()->id.'/print').'" data-toggle="tooltip" title="Print Invoice"><i class="la la-print"></i> Print</a>';
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(NgnDigitalInvoiceRequest::class);

        CRUD::field('invoice_type')->type('select_from_array')->options([
            'repair' => 'Repair', 'rental' => 'Rental', 'sale' => 'Sale', 'service' => 'Service',
        ])->allows_null(false)->default('sale')->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('invoice_category')->type('select_from_array')->options([
            'new' => 'New Bike', 'used' => 'Used Bike', 'parts' => 'Parts', 'service' => 'Service',
        ])->default('new')->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('invoice_number')->type('text')->label('Invoice Number')->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('booking_invoice_id')
            ->label('Booking Invoice')
            ->type('select2')
            ->entity('bookingInvoice')
            ->attribute('detail')
            ->model(BookingInvoice::class)
            ->wrapper(['class' => 'form-group col-md-4 d-none']);

        Widget::add()->type('script')->content('assets/js/admin/forms/get-booking-invoice-details-inline.js');

        CRUD::field('amount')->type('number')->label('Amount')->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('total_paid')->type('number')->label('Total Paid')->wrapper(['class' => 'form-group col-md-4']);

        // CRUD::field('template')->type('select_from_array')->options([
        //     'modern'=>'Modern','classic'=>'Classic','minimal'=>'Minimal'
        // ])->default('modern')->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('customer_id')
            ->label('Customer')
            ->type('select2')
            ->entity('customer')
            ->attribute('detail')
            ->model(Customer::class)
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('customer_name')->wrapper(['class' => 'form-group col-md-2']);
        CRUD::field('customer_email')->wrapper(['class' => 'form-group col-md-2']);
        CRUD::field('customer_phone')->wrapper(['class' => 'form-group col-md-2']);
        CRUD::field('whatsapp')->label('WhatsApp')->wrapper(['class' => 'form-group col-md-2']);

        Widget::add()->type('script')->content('assets/js/admin/forms/get-customer-details-inline.js');

        CRUD::field('motorbike_id')
            ->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('detail')
            ->model(Motorbike::class)
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('registration_number')->label('Reg No.')->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('vin')->label('VIN')->wrapper(['class' => 'form-group col-md-2 d-none']);
        CRUD::field('make')->label('Make')->wrapper(['class' => 'form-group col-md-2 d-none']);
        CRUD::field('model')->label('Model')->wrapper(['class' => 'form-group col-md-2 d-none']);
        CRUD::field('year')->type('number')->label('Year')->wrapper(['class' => 'form-group col-md-2 d-none']);

        Widget::add()->type('script')->content('assets/js/admin/forms/get-motorbike-details-inline.js');

        CRUD::field('issue_date')->type('date')->default(now()->toDateString())->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('due_date')->type('date')->wrapper(['class' => 'form-group col-md-6']);

        // Notes
        CRUD::addField([
            'name' => 'notes',
            'label' => 'Customer Notes',
            'type' => 'textarea',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        // Notes
        CRUD::addField([
            'name' => 'internal_notes',
            'label' => 'Internal Notes',
            'type' => 'textarea',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        // Repeatable items
        CRUD::addField([
            'name' => 'items_repeatable',
            'label' => 'Invoice Items',
            'type' => 'repeatable',
            'fields' => [
                [
                    'name' => 'item_name',
                    'type' => 'text',
                    'label' => 'Item Name',
                    'wrapper' => ['class' => 'form-group col-md-5'],
                ],
                [
                    'name' => 'sku',
                    'type' => 'text',
                    'label' => 'SKU',
                    'wrapper' => ['class' => 'form-group col-md-2 d-none'],
                ],
                [
                    'name' => 'quantity',
                    'type' => 'number',
                    'label' => 'Qty',
                    'default' => 1,
                    'attributes' => ['min' => 1],
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'price',
                    'type' => 'number',
                    'label' => 'Price',
                    'attributes' => ['step' => 'any'],
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'total',
                    'type' => 'number',
                    'label' => 'Total',
                    'attributes' => ['step' => 'any', 'readonly' => 'readonly'],
                    'wrapper' => ['class' => 'form-group col-md-2 d-none'],
                ],
                [
                    'name' => 'notes',
                    'type' => 'textarea',
                    'label' => 'Notes',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
            ],
            'init_rows' => 0,
            'min_rows' => 0,
        ]);

        // Status
        CRUD::field('status')->type('select_from_array')->options([
            'draft' => 'Draft', 'approved' => 'Approved', 'sent' => 'Sent', 'paid' => 'Paid', 'cancelled' => 'Cancelled',
        ])->default('draft')->wrapper(['class' => 'form-group col-md-6']);

        CRUD::setOperationSetting('saveAllInputsRegardlessOfFields', true);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $request = $this->crud->getRequest();
        $response = $this->traitStore();
        $entry = $this->crud->entry ?? null;
        if ($entry) {
            // Sync repeatable items
            $this->syncItems($entry);
            $entry->refresh();
        }

        return $response;
    }

    public function update()
    {
        $request = $this->crud->getRequest();
        $response = $this->traitUpdate();
        $entry = $this->crud->entry ?? null;
        if ($entry) {
            // Sync repeatable items
            $this->syncItems($entry);
            $entry->refresh();
        }

        return $response;
    }

    protected function syncItems(NgnDigitalInvoice $invoice): void
    {
        // Get repeatable items directly from the request, ensure array
        $rows = request()->input('items_repeatable') ?? [];

        if (! is_array($rows)) {
            $rows = [];
        }

        // Delete existing items
        $invoice->items()->delete();

        // Create new items if any
        foreach ($rows as $r) {
            $invoice->items()->create([
                'item_name' => $r['item_name'] ?? '',
                'sku' => $r['sku'] ?? '',
                'quantity' => $r['quantity'] ?? 1,
                'price' => $r['price'] ?? 0,
                'total' => $r['total'] ?? 0,
                'notes' => $r['notes'] ?? null,
            ]);
        }
    }
}
