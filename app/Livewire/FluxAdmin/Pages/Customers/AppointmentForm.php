<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Mail\CustomerAppointmentNotification;
use App\Models\CustomerAppointments;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Customer appointment — Flux Admin')]
class AppointmentForm extends Component
{
    use WithAuthorization;

    public ?CustomerAppointments $customerAppointment = null;

    public array $form = [];

    public bool $sendEmail = false;

    public function mount(?CustomerAppointments $customerAppointment = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->customerAppointment = $customerAppointment;

        if ($customerAppointment && $customerAppointment->exists) {
            $this->form = $customerAppointment->getAttributes();
            $this->form['appointment_date'] = $customerAppointment->appointment_date?->format('Y-m-d\TH:i') ?? '';
            $this->form['is_resolved'] = (bool) $customerAppointment->is_resolved;
        } else {
            $this->form = [
                'appointment_date' => now()->format('Y-m-d\TH:i'),
                'customer_name' => '',
                'registration_number' => '',
                'contact_number' => '',
                'email' => '',
                'booking_reason' => '',
                'is_resolved' => false,
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.appointment_date' => ['required', 'date'],
            'form.customer_name' => ['required', 'string', 'max:255'],
            'form.registration_number' => ['nullable', 'string', 'max:20'],
            'form.contact_number' => ['nullable', 'string', 'max:40'],
            'form.email' => ['nullable', 'email', 'max:191'],
            'form.booking_reason' => ['nullable', 'string', 'max:2000'],
            'form.is_resolved' => ['boolean'],
        ]);

        $payload = [
            'appointment_date' => $this->form['appointment_date'],
            'customer_name' => $this->form['customer_name'],
            'registration_number' => strtoupper((string) ($this->form['registration_number'] ?? '')),
            'contact_number' => $this->form['contact_number'] ?? null,
            'email' => $this->form['email'] ?? null,
            'booking_reason' => $this->form['booking_reason'] ?? null,
            'is_resolved' => (bool) ($this->form['is_resolved'] ?? false),
        ];

        if ($this->customerAppointment && $this->customerAppointment->exists) {
            $this->customerAppointment->update($payload);
            $model = $this->customerAppointment->fresh();
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Appointment updated.');
        } else {
            $model = CustomerAppointments::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Appointment created.');
        }

        if ($this->sendEmail && filled($model->email)) {
            $this->sendEmail($model);
        }

        $this->redirect(route('flux-admin.customer-appointments.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.customers.appointment-form');
    }

    private function sendEmail(CustomerAppointments $appointment): void
    {
        $recipients = array_filter([$appointment->email, 'customerservice@neguinhomotors.co.uk']);
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
        } catch (\Throwable $e) {
            Log::error('Flux Admin appointment email failed: '.$e->getMessage());
        }
    }
}
