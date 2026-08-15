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
        $catfordBranch = $this->catfordBranch();

        if ($motBooking && $motBooking->exists) {
            $attrs = $motBooking->getAttributes();
            $start = null;
            foreach (['start', 'end', 'date_of_appointment'] as $field) {
                if (! empty($attrs[$field])) {
                    try {
                        $parsed = Carbon::parse($attrs[$field]);
                        $attrs[$field] = $parsed->format('Y-m-d\\TH:i');

                        if ($field === 'start') {
                            $start = $parsed;
                        }
                    } catch (\Throwable) {
                        // keep raw
                    }
                }
            }

            if ($start) {
                $end = $this->parseFormDateTime($attrs['end'] ?? null);
                if (! $end || $end->lessThanOrEqualTo($start)) {
                    $attrs['end'] = MOTBooking::appointmentEnd($start)->format('Y-m-d\\TH:i');
                }
            }

            $this->preparePaymentMethodFields($attrs);
            if ($catfordBranch) {
                $attrs['branch_id'] = $catfordBranch->id;
            }
            $this->form = $attrs;
        } else {
            $start = $this->parseCalendarQueryDate(request()->query('start')) ?? now();
            $end = $this->parseCalendarQueryDate(request()->query('end')) ?? MOTBooking::appointmentEnd($start);

            if ($end->lessThanOrEqualTo($start)) {
                $end = MOTBooking::appointmentEnd($start);
            }

            $this->form = [
                'status' => MOTBooking::STATUS_BOOKED,
                'branch_id' => $catfordBranch?->id,
                'is_paid' => false,
                'all_day' => false,
                'is_validate' => true,
                'user_id' => auth()->id(),
                'date_of_appointment' => $start->format('Y-m-d\\TH:i'),
                'start' => $start->format('Y-m-d\\TH:i'),
                'end' => $end->format('Y-m-d\\TH:i'),
                'payment_method_choice' => '',
                'payment_method_custom' => '',
            ];
        }
    }

    public function save(): void
    {
        if ($catfordBranch = $this->catfordBranch()) {
            $this->form['branch_id'] = $catfordBranch->id;
        }

        $this->form['is_paid'] = (bool) ($this->form['is_paid'] ?? false);
        $this->form['is_dealt'] = (bool) ($this->form['is_dealt'] ?? false);
        $this->normaliseDateTimeFields();
        $paymentMethod = $this->normalisedPaymentMethod();

        $this->validate([
            'form.branch_id' => ['required', 'integer', 'exists:branches,id'],
            'form.customer_name' => ['required', 'string', 'max:255'],
            'form.customer_contact' => ['required', 'string', 'max:50'],
            'form.customer_email' => ['required', 'email', 'max:255'],
            'form.vehicle_registration' => ['required', 'string', 'max:20'],
            'form.start' => ['required', 'date'],
            'form.end' => ['nullable', 'date'],
            'form.status' => ['required', 'string', 'in:pending,available,completed,cancelled,booked'],
            'form.is_paid' => ['boolean'],
            'form.is_dealt' => ['boolean'],
            'form.payment_link' => ['nullable', 'string', 'max:500'],
            'form.payment_method_choice' => ['required', 'string', 'in:Cash,Card,Other'],
            'form.payment_method_custom' => ['nullable', 'required_if:form.payment_method_choice,Other', 'string', 'max:120'],
            'form.payment_notes' => ['required', 'string', 'max:2000'],
            'form.notes' => ['required', 'string', 'max:5000'],
        ]);

        $start = $this->parseFormDateTime($this->form['start'] ?? null);
        if (! $start) {
            $this->addError('form.start', 'Slot date and time must be valid.');

            return;
        }

        $end = MOTBooking::appointmentEnd($start);
        $this->form['end'] = $end->format('Y-m-d\\TH:i');

        if ($this->slotAlreadyBooked($start, $end)) {
            $this->addError('form.start', 'That time overlaps an existing MOT booking. Choose a free 30 minute slot, for example 09:30 after a 09:00-09:30 booking.');

            return;
        }

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
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'date_of_appointment' => $start->format('Y-m-d H:i:s'),
            'status' => $status,
            'is_paid' => (bool) ($this->form['is_paid'] ?? false),
            'payment_link' => $this->form['payment_link'] ?? null,
            'payment_method' => $paymentMethod,
            'payment_notes' => $this->form['payment_notes'] ?? null,
            'notes' => $this->form['notes'] ?? null,
            'background_color' => $background,
            'text_color' => $text,
            'all_day' => false,
            'is_validate' => true,
        ];

        if ($this->motBooking && $this->motBooking->exists) {
            $data['is_dealt'] = (bool) ($this->form['is_dealt'] ?? false);
            $this->motBooking->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'MOT booking updated.');
        } else {
            MOTBooking::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'MOT booking created.');
        }

        $this->redirect(route('flux-admin.mot-bookings.index'), navigate: true);
    }

    private function parseCalendarQueryDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function normaliseDateTimeFields(): void
    {
        foreach (['start', 'end', 'date_of_appointment'] as $field) {
            $parsed = $this->parseFormDateTime($this->form[$field] ?? null);
            if ($parsed) {
                $this->form[$field] = $parsed->format('Y-m-d\\TH:i');
            }
        }
    }

    public function updatedFormStart(): void
    {
        $start = $this->parseFormDateTime($this->form['start'] ?? null);
        if (! $start) {
            return;
        }

        $this->form['date_of_appointment'] = $start->format('Y-m-d\\TH:i');
        $this->form['end'] = MOTBooking::appointmentEnd($start)->format('Y-m-d\\TH:i');
    }

    private function parseFormDateTime(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        $formats = [
            'Y-m-d\\TH:i',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd/m/Y, H:i',
            'd/m/Y H:i',
            'd-m-Y H:i',
        ];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $value);
                if ($parsed !== false) {
                    return $parsed;
                }
            } catch (\Throwable) {
                // try next format
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function preparePaymentMethodFields(array &$attrs): void
    {
        $method = trim((string) ($attrs['payment_method'] ?? ''));

        if (in_array($method, ['Cash', 'Card'], true)) {
            $attrs['payment_method_choice'] = $method;
            $attrs['payment_method_custom'] = '';

            return;
        }

        $attrs['payment_method_choice'] = $method === '' ? '' : 'Other';
        $attrs['payment_method_custom'] = $method;
    }

    private function normalisedPaymentMethod(): string
    {
        $choice = (string) ($this->form['payment_method_choice'] ?? '');

        if ($choice === 'Other') {
            return trim((string) ($this->form['payment_method_custom'] ?? ''));
        }

        return $choice;
    }

    private function slotAlreadyBooked(Carbon $start, Carbon $end): bool
    {
        return MOTBooking::hasOverlappingSlot(
            (int) ($this->form['branch_id'] ?? 0),
            $start,
            $end,
            $this->motBooking?->exists ? $this->motBooking->id : null
        );
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

    private function catfordBranch(): ?Branch
    {
        return Branch::query()
            ->where('name', 'like', '%Catford%')
            ->orderBy('id')
            ->first(['id', 'name']);
    }

    public function render()
    {
        $branch = $this->catfordBranch();

        return view('flux-admin.pages.vehicles.mot-booking-form', compact('branch'));
    }
}
