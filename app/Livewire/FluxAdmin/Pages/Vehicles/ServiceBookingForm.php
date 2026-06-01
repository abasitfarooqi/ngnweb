<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ServiceBooking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Service Booking — Flux Admin')]
class ServiceBookingForm extends Component
{
    use WithAuthorization;

    public ?ServiceBooking $serviceBooking = null;

    public array $form = [];

    public function mount(?ServiceBooking $serviceBooking = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->serviceBooking = $serviceBooking;

        if ($serviceBooking && $serviceBooking->exists) {
            $attrs = $serviceBooking->getAttributes();
            foreach (['booking_date'] as $f) {
                if (! empty($attrs[$f])) {
                    try {
                        $attrs[$f] = \Carbon\Carbon::parse($attrs[$f])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$f] = null;
                    }
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'status'       => 'pending',
                'enquiry_type' => 'service_booking',
                'booking_date' => now()->format('Y-m-d'),
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.fullname'     => ['required', 'string', 'max:255'],
            'form.phone'        => ['nullable', 'string', 'max:50'],
            'form.email'        => ['nullable', 'email', 'max:255'],
            'form.reg_no'       => ['nullable', 'string', 'max:20'],
            'form.enquiry_type' => ['nullable', 'string', 'in:service_booking,general'],
            'form.service_type' => ['nullable', 'string', 'max:100'],
            'form.subject'      => ['nullable', 'string', 'max:255'],
            'form.description'  => ['nullable', 'string', 'max:5000'],
            'form.booking_date' => ['nullable', 'date'],
            'form.booking_time' => ['nullable', 'string', 'max:20'],
            'form.status'       => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'form.notes'        => ['nullable', 'string', 'max:5000'],
        ]);

        $data = [
            'fullname'     => $this->form['fullname'] ?? null,
            'phone'        => $this->form['phone'] ?? null,
            'email'        => $this->form['email'] ?? null,
            'reg_no'       => $this->form['reg_no'] ?? null,
            'enquiry_type' => $this->form['enquiry_type'] ?? null,
            'service_type' => $this->form['service_type'] ?? null,
            'subject'      => $this->form['subject'] ?? null,
            'description'  => $this->form['description'] ?? null,
            'booking_date' => $this->form['booking_date'] ?? null,
            'booking_time' => $this->form['booking_time'] ?? null,
            'status'       => $this->form['status'] ?? 'pending',
            'notes'        => $this->form['notes'] ?? null,
        ];

        if ($this->serviceBooking && $this->serviceBooking->exists) {
            $this->serviceBooking->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Service booking updated.');
        } else {
            ServiceBooking::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Service booking created.');
        }

        $this->redirect(route('flux-admin.service-bookings.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.service-booking-form');
    }
}
