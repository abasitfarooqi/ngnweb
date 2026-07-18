<?php

namespace App\Livewire\Site\Mot;

use App\Http\Controllers\MailController;
use App\Models\MOTBooking;
use App\Models\ServiceBooking;
use App\Rules\BookableTimeSlot;
use App\Rules\NotSunday;
use App\Support\BookingSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Book extends Component
{
    public string $branch_id = '';

    public string $branchLabel = 'Catford';

    public string $regNo = '';

    public string $make = '';

    public string $model = '';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $preferredDate = '';

    public string $preferredTime = '';

    public string $notes = '';

    /** Bumped after submit so Flux date-picker and selects remount empty. */
    public int $formNonce = 0;

    public array $timeSlots = [];

    public function mount(): void
    {
        $this->timeSlots = MOTBooking::motTimeSlots();

        $catfordBranchId = MOTBooking::catfordBranchId();
        if ($catfordBranchId) {
            $this->branch_id = (string) $catfordBranchId;
        }

        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;
        $fullName = trim((string) (($profile?->first_name ?? '').' '.($profile?->last_name ?? '')));
        if ($fullName !== '') {
            $this->name = $fullName;
        }
        if ($customerAuth?->email) {
            $this->email = (string) $customerAuth->email;
        }
        if ($profile?->phone) {
            $this->phone = (string) $profile->phone;
        }
    }

    protected function rules(): array
    {
        $catfordBranchId = MOTBooking::catfordBranchId();

        return [
            'branch_id' => array_values(array_filter([
                'required',
                'integer',
                'exists:branches,id',
                $catfordBranchId ? Rule::in([(string) $catfordBranchId, $catfordBranchId]) : null,
            ])),
            'regNo' => ['required', 'string', 'min:2', 'max:10'],
            'name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'preferredDate' => ['required', 'date', 'after_or_equal:today', new NotSunday],
            'preferredTime' => array_values(array_filter([
                'required',
                'string',
                Rule::in(array_keys($this->availableTimeSlots)),
                new BookableTimeSlot($this->preferredDate),
            ])),
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function updatedPreferredDate(): void
    {
        if ($this->preferredTime !== '' && ! array_key_exists($this->preferredTime, $this->availableTimeSlots)) {
            $this->preferredTime = '';
        }
    }

    public function submitBooking(): void
    {
        $this->validate();

        $customerAuth = Auth::guard('customer')->user();
        $customerProfile = $customerAuth?->customer;
        $customerName = trim((string) ($this->name ?: ($customerProfile?->first_name.' '.$customerProfile?->last_name)));
        $customerName = $customerName !== '' ? $customerName : 'Portal customer';
        $customerPhone = trim((string) ($this->phone ?: ($customerProfile?->phone ?? '')));
        $customerEmail = trim((string) ($this->email ?: ($customerAuth?->email ?? '')));
        $appointmentStart = MOTBooking::appointmentStart($this->preferredDate, $this->preferredTime);
        $bookingNotes = implode("\n", array_filter([
            'This made by website frontend user.',
            'We are going to edit it later we made the process ahead.',
            'Source: site.mot.book',
            'Branch: Catford',
            'Registration: '.strtoupper(trim($this->regNo)),
            $this->make !== '' ? 'Make: '.trim($this->make) : null,
            $this->model !== '' ? 'Model: '.trim($this->model) : null,
            $this->notes !== '' ? 'Notes: '.trim($this->notes) : null,
        ]));

        $slotTaken = MOTBooking::query()
            ->where('branch_id', (int) $this->branch_id)
            ->whereDate('date_of_appointment', $this->preferredDate)
            ->where('start', $appointmentStart->toDateTimeString())
            ->where('status', '!=', MOTBooking::STATUS_CANCELLED)
            ->exists();

        if ($slotTaken) {
            $this->addError('preferredTime', 'That time slot has already been reserved.');

            return;
        }

        $serviceBookingId = null;

        DB::transaction(function () use ($appointmentStart, $customerAuth, $customerName, $customerPhone, $customerEmail, $bookingNotes, &$serviceBookingId): void {
            MOTBooking::query()->create([
                'branch_id' => (int) $this->branch_id,
                'vehicle_registration' => strtoupper(trim($this->regNo)),
                'vehicle_chassis' => null,
                'vehicle_color' => null,
                'date_of_appointment' => $appointmentStart->toDateTimeString(),
                'start' => $appointmentStart->toDateTimeString(),
                'end' => $appointmentStart->toDateTimeString(),
                'customer_name' => $customerName,
                'customer_contact' => $customerPhone,
                'customer_email' => $customerEmail,
                'status' => MOTBooking::STATUS_PENDING,
                'title' => null,
                'notes' => $bookingNotes,
                'all_day' => false,
                'is_validate' => true,
                'is_paid' => false,
                'payment_method' => null,
                'payment_notes' => null,
                'user_id' => null,
            ]);

            $serviceBooking = ServiceBooking::query()->create([
                'customer_id' => $customerAuth?->customer_id,
                'customer_auth_id' => $customerAuth?->id,
                'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
                'enquiry_type' => 'mot',
                'service_type' => 'MOT website booking',
                'subject' => 'MOT booking request',
                'description' => implode(' | ', array_filter([
                    'Reg: '.strtoupper(trim($this->regNo)),
                    'Make: '.trim($this->make),
                    'Model: '.trim($this->model),
                    'Date: '.$this->preferredDate,
                    'Time: '.$this->preferredTime,
                    $this->notes !== '' ? 'Notes: '.trim($this->notes) : null,
                    'Source: site.mot.book',
                ])),
                'requires_schedule' => true,
                'booking_date' => $this->preferredDate,
                'booking_time' => $this->preferredTime,
                'status' => 'Pending',
                'fullname' => $customerName,
                'phone' => $customerPhone,
                'reg_no' => strtoupper(trim($this->regNo)),
                'email' => $customerEmail !== '' ? $customerEmail : null,
            ]);

            $serviceBookingId = $serviceBooking->id;
        });

        if ($serviceBookingId) {
            $serviceBooking = ServiceBooking::query()->find($serviceBookingId);
            if ($serviceBooking) {
                app(MailController::class)->sendBookingConfirmation($serviceBooking);
            }
        }

        session()->flash('success', 'MOT booking request received! We will contact you shortly to confirm.');
        $this->resetValidation();
        $this->reset([
            'regNo',
            'make',
            'model',
            'name',
            'email',
            'phone',
            'preferredDate',
            'preferredTime',
            'notes',
        ]);

        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;
        $fullName = trim((string) (($profile?->first_name ?? '').' '.($profile?->last_name ?? '')));
        if ($fullName !== '') {
            $this->name = $fullName;
        }
        if ($customerAuth?->email) {
            $this->email = (string) $customerAuth->email;
        }
        if ($profile?->phone) {
            $this->phone = (string) $profile->phone;
        }
        $this->formNonce++;
    }

    public function getAvailableTimeSlotsProperty(): array
    {
        if ($this->branch_id === '' || $this->preferredDate === '') {
            return $this->timeSlots;
        }

        return MOTBooking::availableTimeSlotsForDate((int) $this->branch_id, $this->preferredDate);
    }

    public function getActiveCustomerBookingProperty(): ?array
    {
        $lookupEmail = trim((string) $this->email);
        if ($lookupEmail === '') {
            $lookupEmail = trim((string) Auth::guard('customer')->user()?->email);
        }

        if ($lookupEmail === '') {
            return null;
        }

        $booking = MOTBooking::query()
            ->with('branch:id,name')
            ->whereRaw('LOWER(customer_email) = ?', [strtolower($lookupEmail)])
            ->where('status', '!=', MOTBooking::STATUS_CANCELLED)
            ->whereDate('date_of_appointment', '>=', today())
            ->orderBy('date_of_appointment')
            ->first();

        if (! $booking) {
            return null;
        }

        return [
            'id' => $booking->id,
            'registration' => $booking->vehicle_registration,
            'date' => Carbon::parse($booking->date_of_appointment)->format('d M Y'),
            'time' => BookingSchedule::formatTimeAmPm(Carbon::parse($booking->start ?? $booking->date_of_appointment)->format('H:i')),
            'status' => $booking->status,
            'branch' => $booking->branch?->name ?? 'Catford',
        ];
    }

    public function render()
    {
        return view('livewire.site.mot.book')
            ->layout('components.layouts.public', [
                'title' => 'Book MOT Test | NGN Motors London',
                'description' => 'Book your motorcycle MOT test online at NGN Motors.',
            ]);
    }
}
