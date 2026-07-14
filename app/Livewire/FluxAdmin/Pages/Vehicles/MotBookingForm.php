<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\MOTBooking;
use Carbon\Carbon;
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
        $this->authorizeModule('see-menu-mot-bookings');
        $this->motBooking = $motBooking;

        if ($motBooking && $motBooking->exists) {
            $attrs = $motBooking->getAttributes();
            foreach (['start', 'end', 'date_of_appointment'] as $field) {
                if (! empty($attrs[$field])) {
                    try {
                        $attrs[$field] = Carbon::parse($attrs[$field])->format('Y-m-d\\TH:i');
                    } catch (\Throwable) {
                        // keep raw
                    }
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'status' => MOTBooking::STATUS_BOOKED,
                'is_paid' => false,
                'all_day' => false,
                'is_validate' => true,
                'user_id' => auth()->id(),
                'date_of_appointment' => now()->format('Y-m-d\\TH:i'),
                'start' => now()->format('Y-m-d\\TH:i'),
                'end' => now()->addHour()->format('Y-m-d\\TH:i'),
            ];
        }
    }

    public function save(): void
    {
        $this->form['is_paid'] = (bool) ($this->form['is_paid'] ?? false);

        $this->validate([
            'form.branch_id' => ['required', 'integer', 'exists:branches,id'],
            'form.customer_name' => ['required', 'string', 'max:255'],
            'form.customer_contact' => ['required', 'string', 'max:50'],
            'form.customer_email' => ['required', 'email', 'max:255'],
            'form.vehicle_registration' => ['required', 'string', 'max:20'],
            'form.start' => ['required', 'date'],
            'form.end' => ['required', 'date', 'after:form.start'],
            'form.status' => ['required', 'string', 'in:pending,available,completed,cancelled,booked'],
            'form.is_paid' => ['boolean'],
            'form.payment_link' => ['nullable', 'string', 'max:500'],
            'form.payment_method' => ['required', 'string', 'max:120'],
            'form.payment_notes' => ['required', 'string', 'max:2000'],
            'form.notes' => ['required', 'string', 'max:5000'],
        ]);

        $status = (string) ($this->form['status'] ?? MOTBooking::STATUS_BOOKED);
        [$background, $text] = $this->coloursForStatus($status);

        $staff = trim((string) (auth()->user()->first_name ?? '').' '.(auth()->user()->last_name ?? ''));
        $vrm = (string) ($this->form['vehicle_registration'] ?? '');
        $customer = (string) ($this->form['customer_name'] ?? '');
        $contact = (string) ($this->form['customer_contact'] ?? '');
        $email = (string) ($this->form['customer_email'] ?? '');
        $title = "{$status} MOT {$vrm} {$customer} {$contact} {$email} - By Staff Name: {$staff}";

        $data = [
            'branch_id' => $this->form['branch_id'] ?? null,
            'user_id' => auth()->id(),
            'title' => $title,
            'customer_name' => $customer,
            'customer_contact' => $contact,
            'customer_email' => $email,
            'vehicle_registration' => $vrm,
            'start' => $this->form['start'] ?? null,
            'end' => $this->form['end'] ?? null,
            'date_of_appointment' => $this->form['start'] ?? ($this->form['date_of_appointment'] ?? now()),
            'status' => $status,
            'is_paid' => (bool) ($this->form['is_paid'] ?? false),
            'payment_link' => $this->form['payment_link'] ?? null,
            'payment_method' => $this->form['payment_method'] ?? null,
            'payment_notes' => $this->form['payment_notes'] ?? null,
            'notes' => $this->form['notes'] ?? null,
            'background_color' => $background,
            'text_color' => $text,
            'all_day' => false,
            'is_validate' => true,
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

    /** @return array{0: string, 1: string} */
    private function coloursForStatus(string $status): array
    {
        return match ($status) {
            MOTBooking::STATUS_BOOKED => ['pink', 'black'],
            MOTBooking::STATUS_COMPLETED => ['#006400', '#FFFFFF'],
            MOTBooking::STATUS_CANCELLED => ['gray', 'white'],
            default => ['yellow', 'black'],
        };
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.vehicles.mot-booking-form', compact('branches'));
    }
}
