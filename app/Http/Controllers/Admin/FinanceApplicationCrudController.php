<?php

// app/Http/Controllers/Admin/FinanceApplicationCrudController.php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FinanceApplicationRequest;
use App\Mail\FinanceContractReview;
use App\Mail\LogBookTransferMail;
use App\Models\ApplicationItem;
use App\Models\Contract;
use App\Models\ContractAccess;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FinanceApplicationCrudController extends BaseCrudController
{
    // use \Backpack\CalendarOperation\CalendarOperation; // removed - L12 incompatible
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::enableExportButtons();
        CRUD::setModel(\App\Models\FinanceApplication::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/finance-application');
        CRUD::setEntityNameStrings('Instalment Contract', 'Instalment Contracts');

        // $user = backpack_user();

        // if(!$user->can('see-finance-application')) {
        //     $this->crud->denyAccess(['list', 'show']);
        // }

        // if(!$user->can('modify-finance-application')) {
        //     $this->crud->denyAccess(['create', 'update']);
        // }

        // if(!$user->can('delete-finance-application')) {
        //     $this->crud->denyAccess(['delete']);
        // }

    }

    // CalendarOperation methods removed - package not compatible with L12

    protected function setupListOperation()
    {
        $this->crud->setOperationSetting('datatablesUrl', $this->crud->getRoute() ?: config('backpack.base.route_prefix').'/finance-application');

        CRUD::addColumn(['name' => 'id', 'type' => 'number', 'label' => 'Contract ID']);

        CRUD::addColumn([
            'name' => 'customer_id',
            'type' => 'select',
            'label' => 'Customer',
            'entity' => 'customer',
            'attribute' => 'first_name',
        ]);

        CRUD::addColumn([
            'name' => 'user_id',
            'type' => 'select',
            'label' => 'User',
            'entity' => 'user',
            'attribute' => 'first_name',
            'model' => "App\Models\User",
        ]);

        CRUD::addColumn(['name' => 'is_posted', 'type' => 'check', 'label' => 'Is Posted?']);
        CRUD::addColumn(['name' => 'deposit', 'type' => 'number', 'label' => 'Deposit']);
        CRUD::addColumn(['name' => 'notes', 'type' => 'textarea', 'label' => 'Notes']);
        CRUD::addColumn(['name' => 'contract_date', 'type' => 'date', 'label' => 'Contract Date']);
        CRUD::addColumn(['name' => 'first_instalment_date', 'type' => 'date', 'label' => 'First Instalment Date']);
        CRUD::addColumn(['name' => 'weekly_instalment', 'type' => 'number', 'label' => 'Monthly Instalment']);

        CRUD::addColumn([
            'name' => 'items',
            'type' => 'relationship',
            'label' => 'Application Items',
            'attribute' => 'motorbike.reg_no',
            'model' => "App\Models\ApplicationItem",
        ]);

        CRUD::addColumn(['name' => 'log_book_sent', 'type' => 'check', 'label' => 'Log Book Sent']);

        // CRUD::addFilter(
        //     [
        //         'name'  => 'is_new_latest',
        //         'type'  => 'select2',
        //         'label' => 'New Latest Contract',
        //     ],
        //     [0 => 'No', 1 => 'Yes'],
        //     function ($value) {
        //         $this->crud->addClause('where', 'is_new_latest', $value);
        //     }
        // );

        // CRUD::addFilter(
        //     [
        //         'name'  => 'is_used_latest',
        //         'type'  => 'select2',
        //         'label' => 'Used Latest Contract',
        //     ],
        //     [0 => 'No', 1 => 'Yes'],
        //     function ($value) {
        //         $this->crud->addClause('where', 'is_used_latest', $value);
        //     }
        // );

        CRUD::addFilter(
            [
                'name' => 'log_book_sent',
                'type' => 'select2',
                'label' => 'LOGBOOK TRANSFER',
            ],
            [
                0 => 'No',
                1 => 'Yes',
            ],
            function ($value) {
                $this->crud->addClause('where', 'log_book_sent', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'is_used',
                'type' => 'select2',
                'label' => 'Vehicle Condition',
            ],
            [
                0 => 'New',
                1 => 'Used',
            ],
            function ($value) {
                $this->crud->addClause('where', 'is_used', $value);
            }
        );

        // CRUD::addFilter(
        //     [
        //         'name'  => 'is_used_latest',
        //         'type'  => 'select2',
        //         'label' => 'Sale Used Vehicle Used Latest',
        //     ],
        //     [
        //         0 => 'No',
        //         1 => 'Yes'
        //     ],
        //     function ($value) {
        //         $this->crud->addClause('where', 'is_used_extended', $value);
        //     }
        // );

        // CRUD::addFilter(
        //     [
        //         'name'  => 'updated_at',
        //         'type'  => 'date_range',
        //         'label' => 'Updated At Range'
        //     ],
        //     false,
        //     function ($value) {
        //         $dates = json_decode($value);
        //         $this->crud->addClause('where', 'updated_at', '>=', $dates->from);
        //         $this->crud->addClause('where', 'updated_at', '<=', $dates->to);
        //     }
        // );

        CRUD::addFilter(
            [
                'name' => 'contract_date',
                'type' => 'date_range',
                'label' => 'Contract Date Range',
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'contract_date', '>=', $dates->from);
                $this->crud->addClause('where', 'contract_date', '<=', $dates->to);
            }
        );

        // CRUD::addFilter(
        //     [
        //         'name'  => 'first_instalment_date',
        //         'type'  => 'date_range',
        //         'label' => 'First Instalment Date Range'
        //     ],
        //     false,
        //     function ($value) {
        //         $dates = json_decode($value);
        //         $this->crud->addClause('where', 'first_instalment_date', '>=', $dates->from);
        //         $this->crud->addClause('where', 'first_instalment_date', '<=', $dates->to);
        //     }
        // );

        // CRUD::addFilter(
        //     [
        //         'name'  => 'logbook_transfer_date',
        //         'type'  => 'date_range',
        //         'label' => 'Logbook Transfer Date Range'
        //     ],
        //     false,
        //     function ($value) {
        //         $dates = json_decode($value);
        //         $this->crud->addClause('where', 'logbook_transfer_date', '>=', $dates->from);
        //         $this->crud->addClause('where', 'logbook_transfer_date', '<=', $dates->to);
        //     }
        // );

        CRUD::addFilter(
            [
                'name' => 'motorbike_reg_no',
                'type' => 'text',
                'label' => 'VRM / REG No',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'application_items', function ($query) use ($value) {
                    $query->whereHas('motorbike', function ($subQuery) use ($value) {
                        $subQuery->where('reg_no', 'LIKE', "%$value%");
                    });
                });

                // Optional feedback message if no results found
                if ($this->crud->count() == 0) {
                    \Alert::info('No records found for this VRM / REG No.')->flash();
                }
            }
        );

        CRUD::addFilter(
            [
                'name' => 'is_cancelled',
                'type' => 'dropdown',
                'label' => 'Cancelled Contract',
            ],
            [
                0 => 'No',
                1 => 'Yes',
            ],
            function ($value) {
                $this->crud->addClause('where', 'is_cancelled', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'customer_name',
                'type' => 'text',
                'label' => 'Customer Name',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'customer', function ($query) use ($value) {
                    $query->whereRaw("concat(first_name, ' ', last_name) LIKE ?", ["%{$value}%"]);
                });

            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(FinanceApplicationRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        $this->crud->addField([
            'name' => 'customer_id',
            'label' => 'Customer',
            'type' => 'select2',
            'entity' => 'customer',
            'model' => Customer::class,
            'attribute' => 'detail',
            'data_source' => url(config('backpack.base.route_prefix').'/finance-application/fetch/customer'),
            'placeholder' => 'Select a customer',
            'minimum_input_length' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ]);

        CRUD::addField(['name' => 'contract_date', 'type' => 'datetime', 'label' => 'Contract Date', 'default' => date('Y-m-d H:i:s'), 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        $currentDate = date('Y-m-d H:i:s');

        $daysForward = 7 - date('N', strtotime($currentDate)) + 5;

        $firstFriday = date('Y-m-d', strtotime("+$daysForward days", strtotime($currentDate)));

        CRUD::addField(['name' => 'first_instalment_date', 'type' => 'date', 'label' => 'First Instalment Date', 'default' => $firstFriday, 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        CRUD::addField(['name' => 'motorbike_price', 'type' => 'number', 'label' => 'Motorbike Price', 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        CRUD::addField([
            'name' => 'weekly_instalment',
            'type' => 'text', // Supports integer and up to two decimals for instalment (e.g. 599, 599.00, 599.12)
            'label' => 'Monthly Instalment',
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'attributes' => [
                'class' => 'form-control monthly-instalment-input',
                'pattern' => '^\d+(\.\d{1,2})?$', // Allows integer, or up to 2 decimals (e.g. 599, 599.00, 599.12)
                'inputmode' => 'decimal', // Show decimal keyboard on mobile
                'step' => '0.01', // Suggests 2 decimal steps for number input fields; text field won't restrict step
                // Note: Validation of value/format should still be enforced server-side
            ]
        ]);

        CRUD::addField(['name' => 'deposit', 'type' => 'number', 'label' => 'Deposit', 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        CRUD::addField(['name' => 'extra_items', 'type' => 'textarea', 'label' => 'Extra Items', 'hint' => 'All additional product, or serivces itemised here to justify EXTRA AMOUNT (if extra amount stated on extra amount option).', 'wrapper' => [
            'class' => 'form-group col-md-8',
        ]]);

        CRUD::addField(['name' => 'extra', 'type' => 'number', 'label' => 'Extra Amount', 'hint' => 'all accessories total amount.', 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        CRUD::addField(['name' => 'notes', 'type' => 'textarea', 'label' => 'Notes', 'hint' => 'Anything worth to note for that customer. FYI, that is not appear to customer at any place. (Internal only)']);

        CRUD::addField([
            'name' => 'no_email',
            'type' => 'checkbox',
            'label' => 'No Email',
            'hint' => 'Unchecked this box if customer must be notified of changes.',
            'default' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-12',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_new',
            'label' => 'New Motorcycle',
            'type' => 'checkbox',
            'wrapper' => ['class' => 'form-group col-md-2 d-none'],
        ]);

        CRUD::addField([
            'name' => 'is_used',
            'label' => 'Used Vehicle',
            'type' => 'checkbox',
            'wrapper' => ['class' => 'form-group col-md-2 d-none'],
        ]);

        CRUD::addField([
            'name' => 'is_used_extended_custom',
            'label' => 'Used Vehicle 18 Months (NGN)',
            'type' => 'checkbox',
            'wrapper' => ['class' => 'form-group col-md-2 d-none'],
        ]);

        CRUD::addField([
            'name' => 'is_new_latest',
            'label' => 'New Latest Contract',
            'type' => 'checkbox',
            'wrapper' => ['class' => 'form-group col-md-2'],
        ]);

        CRUD::addField([
            'name' => 'is_used_latest',
            'label' => 'Used Latest Contract',
            'type' => 'checkbox',
            'wrapper' => ['class' => 'form-group col-md-2'],
        ]);

        // CRUD::addField([
        //     'name' => 'is_monthly',
        //     'type' => 'checkbox',
        //     'label' => 'IS MONTHLY',
        //     'hint' => 'Check this box if the vehicle is sold under monthly finance payment agreement.',
        //     'wrapper' => [
        //         'class' => 'form-group col-md-6',
        //     ],
        //     'show_when' => [
        //         'contract_type' => 'is_new',
        //     ],
        // ]);
        CRUD::addField([
            'name' => 'is_monthly',
            'type' => 'checkbox',
            'label' => 'IS MONTHLY',
            'default' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-6 d-none',
            ],
        ]);

        CRUD::addField(['name' => 'is_posted', 'type' => 'checkbox', 'label' => 'Generate Contract', 'default' => 0, 'hint' => 'Leave Unchecked to save as draft']);

        CRUD::addField([
            'name' => 'insurance_pcn',
            'type' => 'checkbox',
            'label' => 'Insurance or PCN',
            'hint' => 'First Link for Insurance and PCN related Contract',
            'default' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_subscription',
            'type' => 'checkbox',
            'label' => '12 Months Subscription Contract',
            'hint' => 'Check this box to enable 12-month subscription contract option',
            'default' => 0,
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'subscription_option',
            'type' => 'radio',
            'label' => 'Subscription Option',
            'options' => [
                'A' => 'Group A - £299.99/month',
                'B' => 'Group B - £399.99/month',
                'C' => 'Group C - £549.99/month',
                'D' => 'Group D - £649.99/month',
            ],
            'default' => 'A',
            'wrapper' => [
                'class' => 'form-group col-md-12',
                'id' => 'subscription-option-wrapper',
            ],
            'attributes' => [
                'id' => 'subscription-option-field',
            ],
        ]);

        CRUD::addField([
            'name' => 'items',
            'type' => 'repeatable',
            'label' => 'Application Items',
            'fields' => [
                [
                    'name' => 'motorbike_id',
                    'label' => 'Motorbike',
                    'type' => 'select2',
                    'entity' => 'motorbike',
                    'model' => Motorbike::class,
                    'attribute' => 'reg_no',
                    'data_source' => url(config('backpack.base.route_prefix').'/finance-application/fetch/motorbike'),
                    'placeholder' => 'Select a motorbike',
                    'minimum_input_length' => 1,
                ],
                ['name' => 'is_posted', 'type' => 'checkbox', 'label' => 'Is Posted?', 'default' => 1],
                ['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id],
            ],
            'new_item_label' => 'Select Motorbike',
        ]);

        CRUD::addField([
            'name' => 'log_book_sent',
            'type' => 'checkbox',
            'label' => 'Logbook V5C Transfer',
            'default' => 0,
            'hint' => 'Check this box if the logbook has been transferred to the customer.',
            'wrapper' => ['class' => 'd-none'],
        ]);

        Widget::add()->type('script')->content('assets/js/admin/forms/show-is-monthly.js');
        Widget::add()->type('script')->content('assets/js/admin/forms/finance-application-checkboxes.js');

        // CRUD::addField([
        //     'name'  => 'sold_by',
        //     'label' => 'Person who sold the bike',
        //     'type'  => 'hidden',
        //     'default' => backpack_user()->id,
        //     'hint'  => 'Set this carefully at the start. Only one correct person should be entered. This value cannot be changed later.',
        //     'wrapper' => ['class' => 'form-group'],
        // ]);

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        // Include the JavaScript file to handle the field visibility logic
        Widget::add()->type('script')->content('assets/js/admin/forms/logbook-transfer.js');
        Widget::add()->type('script')->content('assets/js/admin/forms/finance-application-checkboxes.js');

        $this->crud->addField([
            'name' => 'customer_id',
            'label' => 'Customer',
            'type' => 'select2',
            'entity' => 'customer',
            'model' => Customer::class,
            'attribute' => 'detail',
            'data_source' => url(config('backpack.base.route_prefix').'/finance-application/fetch/customer'),
            'placeholder' => 'Select a customer',
            'minimum_input_length' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ]);

        CRUD::addField(['name' => 'contract_date', 'type' => 'datetime', 'label' => 'Contract Date', 'default' => date('Y-m-d H:i:s'), 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        $currentDate = date('Y-m-d');

        $daysForward = 7 - date('N', strtotime($currentDate)) + 5;

        $firstFriday = date('Y-m-d', strtotime("+$daysForward days", strtotime($currentDate)));

        CRUD::addField(['name' => 'first_instalment_date', 'type' => 'date', 'label' => 'First Instalment Date', 'default' => $firstFriday, 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        CRUD::addField(['name' => 'motorbike_price', 'type' => 'number', 'label' => 'Motorbike Price', 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);


        CRUD::addField([
            'name' => 'weekly_instalment',
            'type' => 'text', // Supports integer and up to two decimals for instalment (e.g. 599, 599.00, 599.12)
            'label' => 'Monthly Instalment',
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'attributes' => [
                'class' => 'form-control monthly-instalment-input',
                'pattern' => '^\d+(\.\d{1,2})?$', // Allows integer, or up to 2 decimals (e.g. 599, 599.00, 599.12)
                'inputmode' => 'decimal', // Show decimal keyboard on mobile
                'step' => '0.01', // Suggests 2 decimal steps for number input fields; text field won't restrict step
                // Note: Validation of value/format should still be enforced server-side
            ]
        ]);



        CRUD::addField(['name' => 'deposit', 'type' => 'number', 'label' => 'Deposit', 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        CRUD::addField(['name' => 'extra_items', 'type' => 'textarea', 'label' => 'Extra Items', 'wrapper' => [
            'class' => 'form-group col-md-8',
        ]]);

        CRUD::addField(['name' => 'extra', 'type' => 'number', 'label' => 'Extra Amount', 'hint' => 'all accessories total amount.', 'wrapper' => [
            'class' => 'form-group col-md-4',
        ]]);

        CRUD::addField(['name' => 'notes', 'type' => 'textarea', 'label' => 'Notes']);

        CRUD::addField([
            'name' => 'no_email',
            'type' => 'checkbox',
            'label' => 'No Email',
            'hint' => 'Unchecked this box if customer must be notified of changes.',
            'default' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-12',
            ],
        ]);

        // CRUD::addField([
        //     'name' => 'is_monthly',
        //     'type' => 'checkbox',
        //     'label' => 'IS MONTHLY',
        //     'hint' => 'Check this box if the vehicle is sold under monthly finance payment agreement.',
        //     'wrapper' => [
        //         'class' => 'form-group col-md-4',
        //     ],
        // ]);
        CRUD::addField([
            'name' => 'is_monthly',
            'type' => 'checkbox',
            'label' => 'IS MONTHLY',
            'default' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-6 d-none',
            ],
        ]);

        CRUD::addField(['name' => 'is_posted', 'type' => 'checkbox', 'label' => 'Generate Contract', 'default' => 0, 'hint' => 'Check this box to generate the contract. Uncheck this box to save the draft']);

        CRUD::addField([
            'name' => 'logbook_transfer_date',
            'type' => 'datetime_picker',
            'label' => 'Logbook Transfer Date',
            'hint' => 'Date when the logbook was transferred.',
            'wrapper' => ['class' => 'd-none'],
        ]);

        CRUD::addField([
            'name' => 'log_book_sent',
            'type' => 'checkbox',
            'label' => 'Logbook V5C Transfer',
            'default' => 0,
            'hint' => 'Check this box if the logbook has been transferred to the customer.',
        ]);

        CRUD::addField([
            'name' => 'reason_of_cancellation',
            'type' => 'text',
            'label' => 'Reason of Cancellation',
            'hint' => 'Reason for cancelling the contract.',
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_cancelled',
            'type' => 'checkbox',
            'label' => 'Cancel Contract',
            'hint' => 'Only Checked, If contract terminated and we recovered motorbike. Only Thiago or William or senior management can checked this box.',
            'default' => 0,
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ]);

        CRUD::addField([
            'name' => 'cancelled_at',
            'type' => 'hidden',
        ]);

        CRUD::addField([
            'name' => 'insurance_pcn',
            'type' => 'checkbox',
            'label' => 'Insurance or PCN',
            'hint' => 'First Link for Insurance and PCN related Contract',
            'default' => 1,
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'items',
            'type' => 'repeatable',
            'label' => 'Application Items',
            'fields' => [
                [
                    'name' => 'motorbike_id',
                    'label' => 'Motorbike',
                    'type' => 'select2',
                    'entity' => 'motorbike',
                    'model' => Motorbike::class,
                    'attribute' => 'reg_no',
                    'data_source' => url(config('backpack.base.route_prefix').'/finance-application/fetch/motorbike'),
                    'placeholder' => 'Select a motorbike',
                    'minimum_input_length' => 1,
                ],
                ['name' => 'is_posted', 'type' => 'checkbox', 'label' => 'Is Posted?', 'default' => 1],
                ['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id],
            ],
            'new_item_label' => 'Select Motorbike',
        ]);

        CRUD::addSaveAction([
            'name' => 'save_and_edit',
            'redirect' => function ($crud) {
                $entry = $crud->getEntry($crud->getCurrentEntryId());

                if ($entry->is_cancelled && ! $entry->cancelled_at) {
                    $entry->cancelled_at = now();
                    $entry->save();
                } elseif (! $entry->is_cancelled) {
                    $entry->cancelled_at = now();
                    $entry->save();
                }

                return $crud->route.'/'.$entry->getKey().'/edit';
            },
            'visible' => function ($crud) {
                return true;
            },
        ]);

        $entry = $this->crud->getCurrentEntry(); // current model instance

    }

    public function fetchCustomer()
    {
        $search = request()->input('q');
        $customers = \App\Models\Customer::where('first_name', 'like', "%$search%")
            ->orWhere('last_name', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('phone', 'like', "%$search%")
            ->get();

        return response()->json([
            'results' => $customers->map(function ($customer) {
                return ['id' => $customer->id, 'text' => (string) $customer];
            }),
        ]);
    }

    public function fetchMotorbike()
    {
        $search = request()->input('q');
        $motorbikes = \App\Models\Motorbike::where('name', 'like', "%$search%")
            ->orWhere('model', 'like', "%$search%")
            ->get();

        return response()->json([
            'results' => $motorbikes->map(function ($motorbike) {
                return ['id' => $motorbike->id, 'text' => (string) $motorbike];
            }),
        ]);
    }

    public function generateAgreementAccess($financeApplication)
    {
        if ($financeApplication->is_posted) {

            if ($financeApplication->is_cancelled && $financeApplication->cancelled_at == null) {
                $financeApplication->cancelled_at = now();
                $financeApplication->save();

                return;
            } elseif ($financeApplication->is_cancelled && $financeApplication->cancelled_at !== null) {
                return;
            }

            if ($financeApplication->log_book_sent && $financeApplication->log_book_sent !== null) {

                $aa = ApplicationItem::where('application_id', $financeApplication->id)->first();
                $motorbike = Motorbike::find($aa->motorbike_id);

                if ($motorbike) {
                    $motorbike->vehicle_profile_id = 2;
                    $motorbike->save();
                }

                $customer = $financeApplication->customer;
                if ($customer && $customer->email) {
                    $logbookTransferDate = $financeApplication->logbook_transfer_date
                        ? Carbon::parse($financeApplication->logbook_transfer_date)->format('d-m-Y H:i')
                        : now()->format('d-m-Y H:i');

                    $data = [
                        'email' => [$customer->email, 'customerservice@neguinhomotors.co.uk'],
                        'title' => 'Logbook Sent Confirmation',
                        'body' => 'Dear Customer, your logbook has been successfully sent for your application.
                                The logbook was transferred on: '.$logbookTransferDate,
                    ];

                    Mail::to($data['email'])->send(new LogBookTransferMail($data));
                }
            } elseif ($financeApplication->log_book_sent == false && $financeApplication->log_book_sent == null) {
                return;
            } else {
                $customer = $financeApplication->customer;
                if (! $customer) {
                    abort(404, 'Customer not found');
                }

                $passcode = Str::random(12);
                $expiresAt = now()->addDays(1);

                $access = ContractAccess::create([
                    'customer_id' => $financeApplication->customer_id,
                    'application_id' => $financeApplication->id,
                    'passcode' => $passcode,
                    'expires_at' => $expiresAt,
                ]);

                // Get insurance/PCN status (needed for both subscription and regular contracts)
                $isInsuranceOrPCN = request()->boolean('insurance_pcn');
                $isMonthly = request()->boolean('is_monthly');

                // Existing contract type logic
                // $contractType = request()->input('contract_type');

                if (request()->input('is_new')) {
                    if ($isInsuranceOrPCN) {
                        $url = route('finance.ins.show.5m.extended', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    } else {
                        $url = route('finance.show', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    }
                } elseif ($financeApplication->is_used) {
                    if ($isInsuranceOrPCN) {
                        $url = route('finance.ins.show.5m.extended', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    } else {
                        $url = route('finance.show', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    }
                } elseif ($financeApplication->is_used_extended_custom) {
                    if ($isInsuranceOrPCN) {
                        $url = route('finance.ins.show.5m.extended', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    } else {
                        $url = route('finance.ins.show.18m.extended.custom', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    }
                    // ===== NEW LATEST CONTRACT LOGIC =====
                } elseif ($financeApplication->is_new_latest) {
                    // Check if subscription is enabled for new latest
                    if ($financeApplication->is_subscription) {
                        if ($isInsuranceOrPCN) {
                            $url = route('finance.ins.show.merged.new', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                        } else {
                            $url = route('finance.show.merged.new', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                        }
                    } else {
                        $url = $isInsuranceOrPCN
                            ? route('finance.ins.show.latest', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode])
                            : route('finance.show.latest', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    }
                } elseif ($financeApplication->is_used_latest) {
                    // Check if subscription is enabled for used latest
                    if ($financeApplication->is_subscription) {
                        if ($isInsuranceOrPCN) {
                            $url = route('finance.ins.show.merged.used', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                        } else {
                            $url = route('finance.show.merged.used', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                        }
                    } else {
                        $url = $isInsuranceOrPCN
                            ? route('finance.ins.show.used.latest', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode])
                            : route('finance.show.used.latest', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                    }
                } else {
                    // fallback
                    $url = route('finance.show', ['customer_id' => $financeApplication->customer_id, 'passcode' => $passcode]);
                }

                if ($access) {
                    $qrBase64 = '';

                    if (request()->input('no_email')) {
                        return response()->json([
                            'qrImage' => $qrBase64,
                            'url' => $url,
                        ]);
                    } else {
                        if ($this->filterEmails($customer->email)) {
                            $data['email'] = ['customerservice@neguinhomotors.co.uk'];
                        } else {
                            $data['email'] = [$customer->email, 'customerservice@neguinhomotors.co.uk'];
                        }

                        $data['title'] = 'Action Required: Review & Sign Your Sale Agreement';

                        $data['body'] = "Dear valued $customer->first_name $customer->last_name,

                            We kindly request your attention to finalize your contract with Neguinho Motors. To proceed, 
                            
                            please click the following link to review and sign the contract: ".$url.'

                            Thank you for choosing Neguinho Motors for your motorcycle rental or hire needs.';

                        $mailData = [
                            'title' => $data['title'],
                            'customer_name' => $customer->first_name.' '.$customer->last_name,
                            'body' => $data['body'],
                            'url' => $url,
                        ];

                        try {
                            Mail::to($data['email'])->send(new FinanceContractReview($mailData));
                        } catch (\Exception $e) {
                            // Check for domain sending restriction error (550 5.7.1)
                            if (strpos($e->getMessage(), '550 5.7.1') !== false) {
                                throw new \Exception('Email sending failed: The email service is not properly configured. Please contact system administrator.');
                            }
                            // Check for other SMTP errors (5xx codes)
                            if (preg_match('/^5\d{2}/', $e->getMessage())) {
                                throw new \Exception('Email sending failed: SMTP server rejected the request. Please contact system administrator.');
                            }
                            throw new \Exception('Email sending failed. Please try again later.');
                        }

                        return response()->json([
                            'qrImage' => $qrBase64,
                            'url' => $url,
                        ]);
                    }
                }
            }
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