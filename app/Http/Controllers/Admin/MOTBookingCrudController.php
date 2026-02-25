<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MOTBookingRequest;
use App\Models\Branch;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MOTBookingCrudController extends CrudController
{
    use \Backpack\CalendarOperation\CalendarOperation;
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

    public function update()
    {
        $request = $this->crud->getRequest();

        $vehicle_registration = $request->vehicle_registration;
        $status = $request->status;
        $customer_name = $request->customer_name;
        $customer_contact = $request->customer_contact;
        $customer_email = $request->customer_email;
        $staff_name = backpack_user()->first_name.' '.backpack_user()->last_name;

        if ($status === 'booked') {
            $background_color = 'pink';
            $text_color = 'black';
        } elseif ($status === 'completed') {
            $background_color = '#006400';
            $text_color = '#FFFFFF';
        } elseif ($status === 'cancelled') {
            $background_color = 'gray';
            $text_color = 'white';
        } else {
            $background_color = 'yellow';
            $text_color = 'black';
        }

        // else if ($status === 'pending') {
        //     $background_color = 'yellow';
        //     $text_color = 'black';
        // }

        $title = "{$status} MOT {$vehicle_registration} {$customer_name} {$customer_contact} {$customer_email} - By Staff Name: ".$staff_name;

        $request->merge([
            'title' => $title,
            'background_color' => $background_color,
            'text_color' => $text_color,
        ]);

        $response = $this->traitUpdate();

        return $response;
    }

    public function store()
    {
        $vehicle_registration = $this->crud->getRequest()->vehicle_registration;
        $status = $this->crud->getRequest()->status;
        $title = 'MOT BOOKING '.$vehicle_registration.' '.$status;

        if ($status === 'booked') {
            $background_color = 'pink';
            $text_color = 'black';
        } elseif ($status === 'completed') {
            $background_color = '#006400';
            $text_color = '#FFFFFF';
        } elseif ($status === 'cancelled') {
            $background_color = 'gray';
            $text_color = 'white';
        }// else if ($status === 'pending') {
        //     $background_color = 'yellow';
        //     $text_color = 'black';
        // }
        $request = $this->crud->getRequest();
        $request->merge([
            'title' => 'MOT BOOKING '.$request->vehicle_registration.' '.$request->status,
            'background_color' => $background_color,
            'text_color' => $text_color,
        ]);

        $response = $this->traitStore();

        return $response;
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\MOTBooking::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/mot-booking');
        CRUD::setEntityNameStrings('MOT Booking', 'MOT Bookings');

    }

    public function setupCalendarOperation()
    {
        CRUD::setOperationSetting('initial-view', 'listWeek');
        CRUD::setOperationSetting('firstDay', 2);
        CRUD::setOperationSetting('views', ['dayGridMonth', 'timeGridWeek', 'timeGridDay']);
        CRUD::setOperationSetting('editable', true);
        CRUD::setOperationSetting('background_color', fn ($event) => $event->active ? 'green' : 'red');
        CRUD::setOperationSetting('text_color', fn ($event) => $event->active ? 'white' : 'black');
        CRUD::setOperationSetting('with-javascript-widget', true);
        CRUD::setOperationSetting('javascript-configuration', [
            'dayMaxEvents' => false,
        ]);
    }

    protected function getCalendarFieldsMap()
    {
        return [
            'title' => 'title',
            'start' => 'start',
            'end' => 'end',
            'background_color' => 'background_color',
            'text_color' => 'text_color',
            'all_day' => 'all_day',
        ];
    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::column('id')->label('B.ID');
        CRUD::column('title')->label('Title');

        CRUD::column('start')->label('Start')->type('datetime');

        CRUD::column('end')->label('End')->type('datetime');

        CRUD::column('status')->label('Status');
        CRUD::column('vehicle_registration')->label('VRN');
        CRUD::column('customer_name')->label('Customer');
        CRUD::column('customer_contact')->label('Contact');
        CRUD::column('customer_email')->label('Email');
        CRUD::column('payment_link')->label('Payment Link');
        CRUD::column('is_paid')->label('Paid?');

        CRUD::addFilter(
            [
                'name' => 'status',
                'type' => 'select2',
                'label' => 'Status',
            ],
            [
                // 'pending' => 'Pending',
                'booked' => 'Booked',
                'available' => 'Available',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
            function ($value) {
                $this->crud->addClause('where', 'status', $value);
            }
        );

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MOTBookingRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        $start = request()->has('start') ? Carbon::parse(request('start'))->setTimezone(config('app.timezone')) : null;
        $end = request()->has('end') ? Carbon::parse(request('end'))->setTimezone(config('app.timezone')) : null;

        CRUD::field('start')
            ->type('datetime')
            ->wrapper(['class' => 'form-group col-md-4'])
            ->value($start);

        CRUD::field('end')
            ->type('datetime')
            ->wrapper(['class' => 'form-group col-md-4'])
            ->value($end);

        CRUD::field('is_validate')
            ->type('hidden')
            ->value(true);

        CRUD::field('status')->label('Status')->default('Available')
            ->type('select_from_array')
            ->options([
                // 'pending' => 'Pending',
                'booked' => 'Booked',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ])->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('title')->default('MOT Booking')->type('hidden');

        CRUD::field('all_day')
            ->type('hidden')
            ->value(false);

        CRUD::field('date_of_appointment')
            ->type('hidden')
            ->value(now());

        CRUD::field('vehicle_registration')
            ->label('VRM')
            ->wrapper(['class' => 'form-group col-md-2']);

        // CRUD::field('vehicle_chassis')
        //     ->wrapper(['class' => 'form-group col-md-4']);

        // CRUD::field('vehicle_color')
        //     ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('customer_name')
            ->label('Full Name')
            ->default('Customer')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('customer_contact')->label('Telephone')->wrapper(['class' => 'form-group col-md-2']);

        CRUD::field('customer_email')->label('Email')->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('payment_link')
            ->label('Payment Link')->hint('Payment link must be generate from the POS Tablet. If not sure ask Tahir, Thiago or William for the link.');

        CRUD::field('payment_method')
            ->label('Payment Method')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('payment_notes')
            ->label('Payment Notes')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('is_paid')
            ->label('PAID')
            ->hint('Tick this box if customer has paid for MOT booking. Provide payment details in Notes section below.');

        CRUD::field('notes')->type('textarea')->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('background_color')
            ->type('hidden')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->default('#3788d8');

        CRUD::field('text_color')
            ->type('hidden')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->default('#ffffff');

        CRUD::addField([
            'name' => 'branch_id',
            'label' => 'Branch',
            'type' => 'select2',
            'entity' => 'branch',
            'attribute' => 'name',
            'model' => Branch::class,
            'tab' => 'General',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        // CRUD::field('background_color')
        //     ->type('color')
        //     ->wrapper(['class' => 'form-group col-md-6'])
        //     ->default('#f83058');

        // CRUD::field('text_color')
        //     ->type('color')
        //     ->wrapper(['class' => 'form-group col-md-6'])
        //     ->default('#ffffff');

        CRUD::addField([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'enum',
            'options' => [
                // 'pending' => 'Pending',
                'available' => 'Available',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'booked' => 'Booked',
            ],
        ]);
    }

    public function generateAgreementAccess($booking)
    {
        // Fetch the branch address based on branch_id
        $branch = Branch::find($booking->branch_id);
        $branchAddress = $branch ? $branch->address : 'Default address if branch not found';

        $startDateTime = new DateTime($booking->start, new DateTimeZone(config('app.timezone')));
        $endDateTime = new DateTime($booking->end, new DateTimeZone(config('app.timezone')));

        $formattedStartDate = $startDateTime->format('F j, Y, g:i A');
        $formattedEndDate = $endDateTime->format('g:i A');

        $recipients = [$booking->customer_email, 'customerservice@neguinhomotors.co.uk'];
        $data = [
            'customer_name' => $booking->customer_name,
            'vehicle_registration' => $booking->vehicle_registration,
            'vehicle_chassis' => $booking->vehicle_chassis ?? 'N/A',
            'vehicle_color' => $booking->vehicle_color ?? 'N/A',
            'date_of_appointment' => $startDateTime->format('F j, Y'),
            'payment_link' => $booking->payment_link ?? 'N/A',
            'start' => $formattedStartDate,
            'end' => $formattedEndDate,
            'notes' => $booking->notes ?? 'None provided',
            'email' => $booking->customer_email,
            'title' => 'MOT Booking Confirmation',
            'payment_method' => $booking->payment_method ?? 'N/A',
            'payment_notes' => $booking->payment_notes ?? 'N/A',
            'is_paid' => $booking->is_paid ?? false,
            'address' => $branchAddress, // Branch address included here
        ];

        try {
            if ($booking->status == 'cancelled') {
                // Send cancellation notification
                // Mail::to($recipients)->send(new \App\Mail\MOTCancelledNotification($data));
            } elseif ($booking->is_paid) {
                if ($booking->status == 'completed') {
                    // Send completed notification
                    Mail::to($booking->customer_email)->send(new \App\Mail\MOTCompletedNotification($data));
                } else {
                    // Mark as booked and send booking notification
                    $booking->status = 'booked';
                    Mail::to($recipients)->send(new \App\Mail\MOTBookingNotification($data));
                }
            } else {
                // // Mark as pending and send payment reminder
                // $booking->status = 'pending';
                // Mail::to($recipients)->send(new \App\Mail\PaymentReminderNotification($data));
            }
        } catch (\Exception $e) {
            // Handle exceptions, log if necessary
            Log::error('Error sending email for booking: '.$e->getMessage());
        }

    }
}
