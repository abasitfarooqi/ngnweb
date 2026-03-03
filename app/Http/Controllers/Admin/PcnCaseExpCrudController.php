<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PcnCaseRequest;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PcnCase;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PcnCaseExpCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\PcnCase::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/pcn-case-exp');
        CRUD::setEntityNameStrings('PCN CASE - EXP*', 'PCN CASES - EXP*');
        // $this->crud->enableAjaxTable();

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
        CRUD::column('full_amount');
        CRUD::column('reduced_amount');
        // CRUD::column('picture_url');
        CRUD::column('user.first_name')->label('Updated By');
        CRUD::column('note');
        CRUD::column('created_at');

        // add virtual column for and that do calclulate days from date_of_contravention
        // CRUD::addColumn([
        //     'name' => 'updates_link',
        //     'label' => 'View Updates',
        //     'type' => 'model_function',
        //     'function_name' => 'getUpdatesLink',
        //     'escaped' => false,
        // ]);
        // CRUD::addButtonFromModelFunction('line', 'updates_link', 'getUpdatesLink', 'end');

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

        CRUD::enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PcnCaseRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('pcn_number');
        CRUD::field('date_of_contravention')->type('date');
        CRUD::field('time_of_contravention')->type('time');

        CRUD::field('motorbike_id')->label('Motorbike')
            ->type('select2')
            ->entity('motorbike')
            ->attribute('reg_no')->model(Motorbike::class);

        // CRUD::field('customer_id')
        //     ->label('Customer')
        //     ->type('select2')
        //     ->entity('customer')
        //     ->attribute('detail')
        //     ->model(Customer::class);

        CRUD::field('isClosed')->type('checkbox');
        CRUD::field('full_amount');
        CRUD::field('reduced_amount');
        CRUD::addField(['name' => 'picture_url', 'type' => 'hidden']);

        // CRUD::addField([
        //     'name' => 'picture_url',
        //     'label' => 'Picture',
        //     'type' => 'upload',
        //     'upload' => true,
        //     'disk' => 'uploads',
        // ]);

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
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'is_appealed',
                    'label' => 'Appealed',
                    'type' => 'checkbox',
                    'wrapper' => ['class' => 'form-group col-md-1'],
                ],
                [
                    'name' => 'is_paid_by_owner',
                    'label' => 'Paid by Owner',
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

    public function store(Request $request)
    {
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();

        if ($request->hasFile('picture_url')) {
            $file = $request->file('picture_url');
            $filename = time().'-'.$file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'uploads');
            $data['picture_url'] = $path;
        }

        $item = $this->crud->create($request->all());

        return redirect()->route('pcn-case.index');
    }

    public function showUpdates($id)
    {
        $pcnCase = PcnCase::with('updates')->findOrFail($id);

        return view('olders.admin.pcn_case_updates.show', compact('pcnCase'));
    }

    public function getUpdatesLink()
    {
        \Log::info('ID accessed for link: '.$this->id);

        return '<a href="'.route('pcn-case.updates', $this->id).'" class="btn btn-sm btn-link">View Updates</a>';
    }

    public function insertEventExp($model)
    {
        \Log::info('Model: '.json_encode($model));

        $motorbike_id = $model->motorbike_id;
        $date_of_contravention = $model->date_of_contravention;

        $booking = \DB::selectOne("
            SELECT
                APP,
                CID,
                MBID,
                FNAME,
                VRM,
                START_DATE,
                END_DATE
            FROM (
                SELECT
                    'RENTING' AS APP,
                    rb.customer_id AS CID,
                    rbi.motorbike_id AS MBID,
                    c.first_name AS FNAME,
                    m.reg_no AS VRM,
                    rb.start_date AS START_DATE,
                    COALESCE(rbi.end_date, CURDATE()) AS END_DATE
                FROM renting_bookings rb
                INNER JOIN renting_booking_items rbi ON rbi.booking_id = rb.id
                INNER JOIN customers c ON c.id = rb.customer_id
                INNER JOIN motorbikes m ON m.id = rbi.motorbike_id
                WHERE m.id = :motorbike_id

                UNION

                SELECT
                    'INSTALMENT' AS APP,
                    fa.customer_id AS CID,
                    ai.motorbike_id AS MBID,
                    c.first_name AS FNAME,
                    m.reg_no AS VRM,
                    fa.contract_date AS START_DATE,
                    CURDATE() AS END_DATE
                FROM finance_applications fa
                INNER JOIN application_items ai ON ai.application_id = fa.id
                INNER JOIN customers c ON c.id = fa.customer_id
                INNER JOIN motorbikes m ON m.id = ai.motorbike_id
                WHERE m.id = :motorbike_id1

                UNION

                SELECT
                    'COMPANY VEHICLE' AS APP,
                    44 AS CID,
                    m.id AS MBID,
                    'COMPANY VEHICLE' AS FNAME,
                    m.reg_no AS VRM,
                    CURDATE() AS START_DATE,
                    CURDATE() AS END_DATE
                FROM company_vehicles cv
                INNER JOIN motorbikes m ON m.id = cv.motorbike_id
                WHERE m.id = :motorbike_id2
            ) AS combined
            ", ['motorbike_id' => $motorbike_id, 'motorbike_id1' => $motorbike_id, 'motorbike_id2' => $motorbike_id]);

        if (! $booking) {
            \Log::error("No booking found for motorbike ID: $motorbike_id.");

            return false;
        }

        $model->customer_id = $booking->CID;
        \Log::info('Customer ID set in model: '.$model->customer_id);

        $start_date = new \DateTime($booking->START_DATE);
        $end_date = new \DateTime($booking->END_DATE);
        $contravention_date = new \DateTime($date_of_contravention);

        if ($contravention_date >= $start_date && $contravention_date <= $end_date) {
            \Log::info('Date of contravention is within the booking period.');
        } else {
            \Log::error('Date of contravention is outside the booking period.');

            return false;
        }

        $customer_id = $model->customer_id;

        $customer = Customer::find($customer_id);
        if (! $customer) {
            \Log::error("Customer with ID $customer_id not found.");

            return false;
        }

        $motorbike = Motorbike::where('id', $motorbike_id)->first();

        if ($this->shouldIgnoreEmail($customer->email)) {
            $recipients = [$customer->email, 'customerservice@neguinhomotors.co.uk'];
        } else {
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

        try {

            Mail::to($recipients)->send(new \App\Mail\PCNNotify($data));
        } catch (\Exception $e) {
            \Log::info('Error sending email: '.$e->getMessage());
        }
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
}
