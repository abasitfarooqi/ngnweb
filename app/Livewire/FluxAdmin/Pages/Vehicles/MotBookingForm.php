<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\MOTBooking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT Booking — Flux Admin')]
class MotBookingForm extends Component
{
    use WithAuthorization;

    public ?MOTBooking $motBooking = null;

    public array $form = [];

    public function mount(?MOTBooking $motBooking = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->motBooking = $motBooking;

        if ($motBooking && $motBooking->exists) {
            $attrs = $motBooking->getAttributes();
            if (! empty($attrs['date_of_appointment'])) {
                try {
                    $attrs['date_of_appointment'] = \Carbon\Carbon::parse($attrs['date_of_appointment'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['date_of_appointment'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'status'              => 'pending',
                'is_paid'             => false,
                'date_of_appointment' => now()->format('Y-m-d'),
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.branch_id'            => ['nullable', 'integer', 'exists:branches,id'],
            'form.title'                => ['nullable', 'string', 'max:255'],
            'form.customer_name'        => ['required', 'string', 'max:255'],
            'form.customer_contact'     => ['nullable', 'string', 'max:50'],
            'form.customer_email'       => ['nullable', 'email', 'max:255'],
            'form.vehicle_registration' => ['required', 'string', 'max:20'],
            'form.date_of_appointment'  => ['nullable', 'date'],
            'form.status'               => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'form.is_paid'              => ['boolean'],
            'form.notes'                => ['nullable', 'string', 'max:5000'],
        ]);

        $data = [
            'branch_id'            => $this->form['branch_id'] ?? null,
            'title'                => $this->form['title'] ?? null,
            'customer_name'        => $this->form['customer_name'] ?? null,
            'customer_contact'     => $this->form['customer_contact'] ?? null,
            'customer_email'       => $this->form['customer_email'] ?? null,
            'vehicle_registration' => $this->form['vehicle_registration'] ?? null,
            'date_of_appointment'  => $this->form['date_of_appointment'] ?? null,
            'status'               => $this->form['status'] ?? 'pending',
            'is_paid'              => (bool) ($this->form['is_paid'] ?? false),
            'notes'                => $this->form['notes'] ?? null,
        ];

        if ($this->motBooking && $this->motBooking->exists) {
            $this->motBooking->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'MOT booking updated.');
        } else {
            MOTBooking::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'MOT booking created.');
        }

        $this->redirect(route('flux-admin.mot-bookings.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.vehicles.mot-booking-form', compact('branches'));
    }
}
