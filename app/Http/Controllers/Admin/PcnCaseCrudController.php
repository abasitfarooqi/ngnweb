<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PcnCaseExport;
use App\Exports\PcnCaseWithUpdatesExport;
use App\Http\Requests\PcnCaseRequest;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Services\SmsNotificationService;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PcnCaseCrudController extends BaseCrudController
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
        CRUD::setModel(PcnCase::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/pcn-case');
        CRUD::setEntityNameStrings('PCN CASE', 'PCN CASES');

        CRUD::enableExportButtons();

    }

    protected function setupListOperation()
    {
        CRUD::column('pcn_number')->label('PCN NO.');
        CRUD::column('date_of_contravention')->label('DATE of Contr.');
        CRUD::column('time_of_contravention')->label('TIME of Contr.');

        CRUD::column('days_since_contravention')->label('Elapsed Days')->type('model_function')->function_name('getDaysSinceContravention');

        CRUD::column('motorbike.reg_no')->label('VRN');
        CRUD::column('customer_id')->label('CUSTOMER');
        CRUD::column('customer.email')->label('EMAIL');
        CRUD::column('isClosed')->label('CASE CLOSED');
        CRUD::column('has_been_appealed')->type('boolean')->label('EVER APPEALED');
        CRUD::column('full_amount');
        CRUD::column('reduced_amount');

        CRUD::column('user.first_name')->label('Updated By');
        CRUD::column('note');
        CRUD::column('created_at');

        // Custom Excel export buttons
        CRUD::addButtonFromView('top', 'pcn_export_summary', 'pcn_export_summary', 'beginning');
        CRUD::addButtonFromView('top', 'pcn_export_with_updates', 'pcn_export_with_updates', 'beginning');

        // Sort by Created Date
        CRUD::addFilter(
            [
                'name' => 'sort_created_at',
                'type' => 'dropdown',
                'label' => 'Sort by Created Date',
            ],
            [
                'asc' => 'Oldest First',
                'desc' => 'Newest First',
            ],
            function ($value) {
                $this->crud->orderBy('created_at', $value);
            }
        );

        // Sort by Date of Contravention
        CRUD::addFilter(
            [
                'name' => 'sort_date_of_contravention',
                'type' => 'dropdown',
                'label' => 'Sort by Date of Contravention',
            ],
            [
                'asc' => 'Oldest First',
                'desc' => 'Newest First',
            ],
            function ($value) {
                $this->crud->orderBy('date_of_contravention', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'update_status',
                'type' => 'dropdown',
                'label' => 'Update Status',
            ],
            [
                'cancelled' => 'Cancelled',
                'transferred' => 'Transferred',
            ],
            function ($value) {
                if ($value === 'cancelled') {
                    $this->crud->addClause('whereHas', 'updates', function ($query) {
                        $query->where('is_cancled', true);
                    });
                } elseif ($value === 'transferred') {
                    $this->crud->addClause('whereHas', 'updates', function ($query) {
                        $query->where('is_transferred', true);
                    });
                }
            }
        );

        CRUD::addFilter(
            [
                'name' => 'date_of_contravention',
                'type' => 'date_range',
                'label' => 'Date of Contravention',
            ],
            false,
            function ($value) {
                $value = json_decode($value, true);

                if (isset($value['from']) && isset($value['to'])) {
                    $startDate = date('Y-m-d', strtotime($value['from']));
                    $endDate = date('Y-m-d', strtotime($value['to']));
                    $this->crud->addClause('whereBetween', 'date_of_contravention', [$startDate, $endDate]);
                } else {
                    \Log::error(__FILE__.' at line '.__LINE__.'Invalid or incomplete date range provided: '.json_encode($value));
                }
            }
        );

        CRUD::addFilter(
            [
                'name' => 'motorbike_reg_no',
                'type' => 'text',
                'label' => 'VRM / REG No',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'motorbike', function ($query) use ($value) {
                    $query->where('reg_no', 'LIKE', "%$value%");
                });
            }
        );

        CRUD::addFilter(
            [
                'name' => 'customer_email',
                'type' => 'text',
                'label' => 'EMAIL',
            ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas', 'customer', function ($query) use ($value) {
                    $query->where('email', 'LIKE', "%$value%");
                });
            }
        );

        CRUD::addFilter(
            [
                'name' => 'has_been_appealed',
                'type' => 'select2',
                'label' => 'EVER APPEALED',
            ],
            [
                0 => 'No',
                1 => 'Yes',
            ],
            function ($value) {
                if ($value == 1) {
                    $this->crud->addClause('whereHas', 'updates', function ($query) {
                        $query->where('is_appealed', true);
                    });
                } else {
                    $this->crud->addClause('whereDoesntHave', 'updates', function ($query) {
                        $query->where('is_appealed', true);
                    });
                }
            }
        );

        CRUD::addFilter(
            [
                'name' => 'isClosed',
                'type' => 'select2',
                'label' => 'CASE CLOSED',
            ],
            [
                0 => 'No',
                1 => 'Yes',
            ],
            function ($value) {
                $this->crud->addClause('where', 'isClosed', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'customer_id',
                'type' => 'select2',
                'label' => 'CUSTOMER',
            ],
            function () {
                return Customer::all()->pluck('detail', 'id')->toArray();
            },
            function ($value) {
                $this->crud->addClause('where', 'customer_id', $value);
            }
        );

        // CRUD::addFilter(
        //     [
        //         'name'  => 'is_police',
        //         'type'  => 'select2',
        //         'label' => 'Police Case',
        //     ],
        //     [
        //         0 => 'No',
        //         1 => 'Yes'
        //     ],
        //     function ($value) {
        //         $this->crud->addClause('where', 'is_police', $value);
        //     }
        // );

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PcnCaseRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('pcn_number')->wrapper(['class' => 'form-group col-md-3']);
        CRUD::field('date_of_contravention')->type('date')->wrapper(['class' => 'form-group col-md-2']);
        CRUD::field('time_of_contravention')->label('Time')->type('time')->wrapper(['class' => 'form-group col-md-1']);
        CRUD::field('council_link')->label('Payment Link')->wrapper(['class' => 'form-group col-md-6']);
        // CRUD::field([
        //     'name' => 'model',
        //     'label' => 'Council',
        //     'type' => 'select2_from_array',
        //     'options' => [
        //         'LEWISHAM' => 'https://pcnevidence.lewisham.gov.uk/pcnonline/',
        //         'SOUTHWARK' => 'https://pcnevidence.southwarkparking.co.uk/pcnonline/'
        //     ],
        //     'allows_null' => false,
        //     'fake' => true,
        //     'store_in' => 'model'
        // ]);

        CRUD::field('motorbike_id')->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('reg_no')->model(Motorbike::class);

        CRUD::field('customer_id')
            ->label('Customer')
            ->type('select2')
            ->entity('customer')
            ->attribute('detail')
            ->model(Customer::class);

        CRUD::field('is_police')->type('checkbox')->label('Police Case');
        CRUD::field('isClosed')->type('checkbox')->label('Case Closed');

        CRUD::field('full_amount')->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('reduced_amount')->label('Reduced Amount (If paid within 14 days)')->wrapper(['class' => 'form-group col-md-6']);
        CRUD::addField(['name' => 'picture_url', 'type' => 'hidden']);
        CRUD::addField([
            'name' => 'date_of_letter_issued',
            'label' => 'Date of Letter Issued',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => true,
                'format' => 'dd/mm/yyyy',
            ],
        ]);
        CRUD::field('note')->type('textarea');
        CRUD::field('send_email')->type('hidden')->default(true);
        CRUD::addField([
            'name' => 'updates',
            'label' => 'Case Updates',
            'type' => 'repeatable',
            'fields' => [
                [
                    'name' => 'user_id',
                    'type' => 'hidden',
                    'default' => backpack_user()->id,
                ],
                [
                    'name' => 'update_date',
                    'type' => 'datetime_picker',
                    'label' => 'Update Date',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'additional_fee',
                    'label' => 'Additional Fee',
                    'type' => 'number',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'note',
                    'label' => 'Notes',
                    'type' => 'text',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                    'attributes' => [
                        'required' => 'required',
                    ],
                ],
                [
                    'name' => 'is_appealed',
                    'label' => 'Appealed',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_paid_by_owner',
                    'label' => 'Paid by NGN',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_paid_by_keeper',
                    'label' => 'Paid by Hirer',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_transferred',
                    'label' => 'Transferred',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_cancled',
                    'label' => 'Canceled',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
            ],
            'new_item_label' => 'Add Update',
            'init_rows' => 0,
            'min_rows' => 0,
            'max_rows' => 10,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        CRUD::addField([
            'name' => 'date_of_letter_issued',
            'label' => 'Date of Letter Issued',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => true,
                'format' => 'dd/mm/yyyy',
            ],
        ]);
        // show checkbox on update
        CRUD::field('send_email')->type('checkbox')->label('Send Email')->wrapper(['class' => 'form-group col-md-12'])->default(false)->hint('Only check this box if you believe the notification email has not been delivered to the customer');

        CRUD::addField([
            'name' => 'copy_letter',
            'type' => 'view',
            'view' => 'livewire.agreements.migrated.admin.pcn_case_updates.copy_letter',
        ]);

        CRUD::addField([
            'name' => 'updates',
            'label' => 'Case Updates',
            'type' => 'repeatable',
            'fields' => [
                [
                    'name' => 'user_id',
                    'type' => 'hidden',
                    'default' => backpack_user()->id,
                ],
                [
                    'name' => 'update_date',
                    'type' => 'datetime_picker',
                    'label' => 'Update Date',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'additional_fee',
                    'label' => 'Additional Fee',
                    'type' => 'number',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'note',
                    'label' => 'Notes',
                    'type' => 'text',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'is_appealed',
                    'label' => 'Appealed',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_paid_by_owner',
                    'label' => 'Paid by NGN',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_paid_by_keeper',
                    'label' => 'Paid by Hirer',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_transferred',
                    'label' => 'Transferred',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_cancled',
                    'label' => 'Canceled',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                // [
                //     'name' => 'id', // This is the update's id
                //     'type' => 'hidden',
                // ],
                // [
                //     'name' => 'pdf_button',
                //     'type' => 'custom_html',
                //     'value' => '<span class="pdf-button-placeholder"></span>',
                // ],
            ],
            'new_item_label' => 'Add Update',
            'init_rows' => 0,
            'min_rows' => 0,
            'max_rows' => 10,
        ]);

        // CRUD::addField([
        //     'name'  => 'tol_requests',
        //     'label' => 'TOL Requests',
        //     'type'  => 'view',
        //     'view'  => 'admin.pcn_case_updates.tol_requests', // Blade view file
        //     'tab'   => 'TOL Requests',
        //     'data'  => [
        //         'tolRequests' => $this->crud->getCurrentEntry()->tolRequests ?? collect()
        //     ],
        // ]);

        Widget::add()->type('script')->inline()->content('assets/js/admin/forms/pcn-tol-request.js');

    }

    public function showUpdates($id)
    {
        $pcnCase = PcnCase::with('updates')->findOrFail($id);

        return view('livewire.agreements.migrated.admin.pcn_case_updates.show', compact('pcnCase'));
    }

    public function getUpdatesLink()
    {
        \Log::info('ID accessed for link: '.$this->id);

        return '<a href="'.route('pcn-case.updates', $this->id).'" class="btn btn-sm btn-link">View Updates</a>';
    }

    public function store()
    {
        $request = $this->crud->getRequest();

        // Ensure send_email = true on create
        $request->merge(['send_email' => true]);

        $response = $this->traitStore();
        $entry = $this->crud->entry ?? null;

        if ($entry) {
            $entry->refresh();

            // Send email if send_email is true
            if ($request->input('send_email')) {
                $this->sendEmail($entry->id);
            }

            // Always send SMS on insert
            $this->sendSms($entry->id);
            \Log::info('SMS sent successfully to on insert entry: '.$entry->id);
        }

        return $response;
    }

    public function update()
    {
        $request = $this->crud->getRequest();
        $response = $this->traitUpdate();
        $entry = $this->crud->entry ?? null;

        if ($entry) {
            $entry->refresh();

            // Send email only if checkbox is ticked
            if ($request->input('send_email')) {
                $this->sendEmail($entry->id);
            }
        }

        return $response;
    }

    protected function sendEmail($id)
    {
        $pcnCase = PcnCase::with(['customer', 'motorbike'])->findOrFail($id);
        if (! $pcnCase) {
            return;
        }

        $customer = $pcnCase->customer;
        $motorbike = $pcnCase->motorbike;

        if (! $customer || ! $motorbike) {
            return;
        }

        // Skip sending if email should be ignored
        $shouldIgnore = $this->shouldIgnoreEmail($customer->email);
        if ($shouldIgnore === true) {
            \Log::info('NOT CUSTOMER: '.$customer->email);
            $recipients = ['customerservice@neguinhomotors.co.uk'];
        } else {
            $recipients = [$customer->email, 'customerservice@neguinhomotors.co.uk'];
        }

        $data = [
            'customer' => $customer->first_name.' '.$customer->last_name,
            'reg_no' => $motorbike->reg_no,
            'date_of_contravention' => $pcnCase->date_of_contravention,
            'pcn_number' => $pcnCase->pcn_number,
            'council_link' => $pcnCase->council_link,
        ];

        \Log::info('Data: '.json_encode($data));

        if (! $pcnCase->isClosed) {
            try {
                if ($pcnCase->is_police) {
                    Mail::to($recipients)->send(new \App\Mail\PCNPoliceNotify($data));
                } else {
                    Mail::to($recipients)->send(new \App\Mail\PCNNotify($data));
                }
                \Log::info('Email sent successfully to: '.implode(', ', $recipients));
            } catch (\Exception $e) {
                \Log::error('Error sending email: '.$e->getMessage());
            }
        }
    }

    protected function sendSms($id)
    {
        $pcnCase = PcnCase::with(['customer', 'motorbike'])->findOrFail($id);
        if (! $pcnCase) {
            return;
        }

        $customer = $pcnCase->customer;
        $motorbike = $pcnCase->motorbike;

        if (! $customer || ! $motorbike) {
            return;
        }

        $smsService = app(SmsNotificationService::class);
        $customerPhoneNumber = preg_replace('/[\s\-()]/', '', $customer->phone);

        if ($this->checkValidUKNumber($customerPhoneNumber)) {
            $smsMessage = "Dear {$customer->first_name} {$customer->last_name}, this is a reminder for Penalty Charge Notice {$pcnCase->pcn_number} regarding vehicle {$motorbike->reg_no}. The outstanding amount of £{$pcnCase->reduced_amount} is unpaid. Please make payment promptly to avoid increases. If you've already paid, contact us at 0208 314 1498 or WhatsApp 07951790568, NGN Motors.";

            try {
                $smsService->sendSms($customerPhoneNumber, $smsMessage);
                \Log::info("SMS sent to customer ID: {$customer->id}, Phone: $customerPhoneNumber");
            } catch (\Exception $e) {
                \Log::error("Failed to send SMS to customer ID: {$customer->id}. Error: ".$e->getMessage());
            }
        } else {
            \Log::warning("Invalid phone number for customer ID: {$customer->id}, Phone: $customerPhoneNumber");
        }
    }

    public function checkValidUKNumber($phoneNumber)
    {
        return preg_match('/^(\+44|07)\d{9,10}$/', $phoneNumber);
    }

    protected function shouldIgnoreEmail($email)
    {
        \Log::info('Checking email: '.$email);

        $patterns = [
            '/\d+no@/',
            '/email\.ngm$/',
            '/email\.com-$/',
            '/-[a-zA-Z0-9]+@/',
        ];

        foreach ($patterns as $pattern) {
            \Log::info('Checking pattern: '.$pattern);
            if (preg_match($pattern, $email)) {
                \Log::info('Email ignored due to pattern: '.$pattern);

                return true;
            }
        }

        \Log::info('Email not ignored: '.$email);

        return false;
    }

    public function exportSummary()
    {
        return Excel::download(new PcnCaseExport, 'pcn_cases_summary.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Backwards-compatible alias for existing export route.
     */
    public function export()
    {
        return $this->exportSummary();
    }

    public function exportWithUpdates()
    {
        return Excel::download(new PcnCaseWithUpdatesExport, 'pcn_cases_with_updates.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function generateTolPdfButton()
    {
        return '<a href="'.backpack_url('pcn-tol-request/create?update_id='.$this->id).'" target="_blank" class="btn btn-sm btn-primary"><i class="la la-file-pdf"></i> PDF</a>';
    }

    public function generateTolLetterPdf($id)
    {
        $tolRequest = \App\Models\PcnTolRequest::with(
            'pcnCaseUpdate.pcnCase.customer',
            'pcnCaseUpdate.pcnCase.motorbike',
            'user'
        )->findOrFail($id);

        $pdf = Pdf::loadView('pcn.template.tol_letter', [
            'pcnNumber' => $tolRequest->pcnCaseUpdate->pcnCase->pcn_number ?? '',
            'customerName' => optional($tolRequest->pcnCaseUpdate->pcnCase->customer)->full_name ?? '',
            'vehicleVrm' => $tolRequest->pcnCaseUpdate->pcnCase->motorbike->reg_no ?? '',
            'userName' => $tolRequest->user->full_name ?? '',
        ]);

        $fileName = 'tol_request_'.$tolRequest->id.'.pdf';

        return $pdf->download($fileName);
    }
}
