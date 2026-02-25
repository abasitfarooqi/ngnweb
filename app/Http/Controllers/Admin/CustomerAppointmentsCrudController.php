<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerAppointmentsRequest;
use App\Mail\CustomerAppointmentNotification;
use App\Models\CustomerAppointments;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CustomerAppointmentsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\CustomerAppointments::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customer-appointments');
        CRUD::setEntityNameStrings('customer appointments', 'customer appointments');

    }

    protected function setupListOperation()
    {
        CRUD::setFromDb();
        CRUD::enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CustomerAppointmentsRequest::class);
        CRUD::setFromDb();

        CRUD::addField([
            'name' => 'appointment_date',
            'type' => 'datetime_picker',
            'label' => 'Appointment Date',
            'default' => date('Y-m-d H:i:s'),
        ]);

        CRUD::addField([
            'name' => 'customer_name',
            'type' => 'text',
            'label' => 'Customer Name',
        ]);

        CRUD::addField([
            'name' => 'registration_number',
            'type' => 'text',
            'label' => 'Registration Number',
            'attributes' => [
                'style' => 'text-transform: uppercase;',
            ],
        ]);

        CRUD::addField([
            'name' => 'contact_number',
            'type' => 'text',
            'label' => 'Contact Number',
        ]);

        CRUD::addField([
            'name' => 'email',
            'type' => 'email',
            'label' => 'Email',
        ]);

        CRUD::addField([
            'name' => 'is_resolved',
            'type' => 'checkbox',
            'label' => 'Resolved',
        ]);

        CRUD::addField([
            'name' => 'booking_reason',
            'type' => 'textarea',
            'label' => 'Booking Reason',
        ]);

        CRUD::addField([
            'name' => 'send_email',
            'type' => 'checkbox',
            'label' => 'Send Email',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store(Request $request)
    {
        $this->crud->hasAccessOrFail('create');
        \Log::info('Access granted for creating a new appointment.');

        $request = $this->crud->validateRequest();
        \Log::info('Request validated successfully.');

        // Create the entry in the database
        $item = $this->crud->create($request->all());
        \Log::info('New appointment created with data: ', $request->all());

        // Manually retrieve the newly created appointment
        $appointment = CustomerAppointments::find($item->id);
        \Log::info('Retrieved newly created appointment: ', [$appointment]);

        if ($appointment && $request->has('send_email') && $request->get('send_email')) {
            $recipients = [$appointment->email, 'customerservice@neguinhomotors.co.uk'];

            $data = [
                'appointment_date' => $appointment->appointment_date,
                'is_resolved' => $appointment->is_resolved,
                'customer_name' => $appointment->customer_name,
                'registration_number' => $appointment->registration_number,
                'contact_number' => $appointment->contact_number,
                'email' => $appointment->email,
                'booking_reason' => $appointment->booking_reason,
            ];

            try {
                Mail::to($recipients)->send(new CustomerAppointmentNotification($data));
                \Log::info('Email sent to: '.implode(', ', $recipients));
            } catch (\Exception $e) {
                \Log::error('Error sending email: '.$e->getMessage());
            }
        } else {
            \Log::info('Email not sent, send_email flag is not set or appointment is null.');
        }

        return redirect()->route('customer-appointments.index');
    }

    public function update(Request $request, $id)
    {
        $this->crud->hasAccessOrFail('update');
        \Log::info('Access granted for updating an appointment.');

        $request = $this->crud->validateRequest();
        \Log::info('Request validated successfully.');

        // Update the entry in the database
        $item = $this->crud->update($id, $request->all());
        \Log::info('Appointment updated with data: ', $request->all());

        // Manually retrieve the updated appointment
        $appointment = CustomerAppointments::find($id);
        \Log::info('Retrieved updated appointment: ', [$appointment]);

        $this->sendEmailNotification($request, $appointment);

        return redirect()->route('customer-appointments.index');
    }

    protected function sendEmailNotification($request, $appointment)
    {
        if ($request->has('send_email') && $request->get('send_email') && $appointment) {
            $recipients = [$appointment->email, 'customerservice@neguinhomotors.co.uk'];
            \Log::info('Preparing to send email to: '.implode(', ', $recipients));

            $data = [
                'appointment_date' => $appointment->appointment_date,
                'customer_name' => $appointment->customer_name,
                'registration_number' => $appointment->registration_number,
                'contact_number' => $appointment->contact_number,
                'email' => $appointment->email,
                'booking_reason' => $appointment->booking_reason,
                'is_resolved' => $appointment->is_resolved,
            ];

            try {
                Mail::to($recipients)->send(new CustomerAppointmentNotification($data));
                \Log::info('Email sent successfully to: '.implode(', ', $recipients));
            } catch (\Exception $e) {
                \Log::error('Error sending email: '.$e->getMessage());
            }
        } else {
            \Log::info('Email not sent, send_email flag is not set or appointment is null.');
        }
    }
}
