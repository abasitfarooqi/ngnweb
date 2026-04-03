<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PcnCaseExport;
use App\Http\Requests\PcnCaseRequest;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Services\SmsNotificationService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PcnCaseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\PcnCase::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/pcn-case');
        CRUD::setEntityNameStrings('PCN CASE', 'PCN CASES');
        CRUD::enableExportButtons();

        $this->crud->addButtonFromModelFunction('bottom', 'export_button', 'getExportButton', 'beginning');
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

        CRUD::field('note')->type('textarea');

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
                    'wrapper' => ['class' => 'form-group col-md-3'],
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

    public function insertEvent($model)
    {
        $customer_id = $model->customer_id;
        $motorbike_id = $model->motorbike_id;
        $date_of_contravention = $model->date_of_contravention;

        $customer = Customer::find($customer_id);
        $motorbike = Motorbike::where('id', $motorbike_id)->first();

        \Log::info('Customer: '.json_encode($customer));
        \Log::info('Customer email: '.$customer->email);

        $shouldIgnore = $this->shouldIgnoreEmail($customer->email);
        \Log::info('Should ignore email: '.($shouldIgnore ? 'true' : 'false'));

        if ($shouldIgnore === false) {
            $recipients = [$customer->email, 'customerservice@neguinhomotors.co.uk'];
        } else {
            \Log::info('NOT CUSTOMER: '.$customer->email);
            $recipients = ['customerservice@neguinhomotors.co.uk'];
        }

        $data = [
            'customer' => $customer->first_name.' '.$customer->last_name,
            'reg_no' => $motorbike->reg_no,
            'date_of_contravention' => $date_of_contravention,
            'pcn_number' => $model->pcn_number,
            'council_link' => $model->council_link,
        ];

        \Log::info('Data: '.json_encode($data));

        if (! $model->isClosed) {
            \Log::info('HITTING MAIL');
            if ($model->is_police) {
                try {
                    Mail::to($recipients)->send(new \App\Mail\PCNPoliceNotify($data));
                } catch (\Exception $e) {
                    \Log::info('Error sending email: '.$e->getMessage());
                }
            } else {
                try {
                    Mail::to($recipients)->send(new \App\Mail\PCNNotify($data));
                } catch (\Exception $e) {
                    \Log::info('Error sending email: '.$e->getMessage());
                }
            }
        } else {
            \Log::info('NOT HITTING MAIL');
        }

        // Send SMS notification
        $smsService = app(SmsNotificationService::class);
        $customerPhoneNumber = $customer->phone;
        $customerPhoneNumber = preg_replace('/[\s\-()]/', '', $customerPhoneNumber);

        if ($this->checkValidUKNumber($customerPhoneNumber)) {
            $customerName = $customer->first_name.' '.$customer->last_name;
            $pcnNumber = $model->pcn_number;
            $vehicleReg = $motorbike->reg_no;
            $amountDue = $model->reduced_amount;
            // $smsMessage = "Hi {$customer->first_name}, your PCN {$model->pcn_number} for {$motorbike->reg_no} was issued on {$date_of_contravention}. please call 0208 314 1498 or WhatsApp 07951790568 or check email.";
            //   $smsMessage = "Dear {$customer->first_name}, this is a reminder for Penalty Charge Notice {$model->pcn_number} regarding vehicle {$motorbike->reg_no}. Immediately contact us at 0208 314 1498 or WhatsApp 07951790568, NGN Motors.";

            $smsMessage = "Dear $customerName, this is a reminder for Penalty Charge Notice $pcnNumber regarding vehicle $vehicleReg. The outstanding amount of £$amountDue is unpaid. Please make payment promptly to avoid increases. If you've already paid, contact us at 0208 314 1498 or WhatsApp 07951790568, NGN Motors.";

            try {
                // Remove spaces, dashes, and brackets before validation

                $smsService->sendSms($customerPhoneNumber, $smsMessage);
                \Log::info("SMS sent to customer ID: {$customer->id}, Phone: $customerPhoneNumber, Message: $smsMessage");
            } catch (\Exception $e) {
                \Log::error("Failed to send SMS to customer ID: {$customer->id}, Phone: $customerPhoneNumber. Error: ".$e->getMessage());
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

    public function export()
    {
        return Excel::download(new PcnCaseExport, 'pcn_cases.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
