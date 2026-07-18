<?php

namespace App\Livewire\Portal\MOT;

use App\Http\Controllers\MailController;
use App\Models\Branch;
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

    public string $date_of_appointment = '';

    public string $time_slot = '';

    public string $motorbike_reg_no = '';

    public string $motorbike_make = '';

    public string $motorbike_model = '';

    public string $notes = '';

    public array $timeSlots = [];

    public ?array $activeCustomerBooking = null;

    public function mount(): void
    {
        $this->timeSlots = MOTBooking::motTimeSlots();

        $catfordBranchId = MOTBooking::catfordBranchId()
            ?? (int) Branch::query()->orderBy('id')->value('id');

        if ($catfordBranchId > 0) {
            $this->branch_id = (string) $catfordBranchId;
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
            'motorbike_reg_no' => ['required', 'string', 'min:2', 'max:10'],
            'motorbike_make' => ['nullable', 'string', 'min:2', 'max:50'],
            'motorbike_model' => ['nullable', 'string', 'min:2', 'max:50'],
            'date_of_appointment' => ['required', 'date', 'after_or_equal:today', new NotSunday],
            'time_slot' => array_values(array_filter([
                'required',
                'string',
                Rule::in(array_keys($this->availableTimeSlots)),
                new BookableTimeSlot($this->date_of_appointment),
            ])),
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function updatedDateOfAppointment(): void
    {
        if ($this->time_slot !== '' && ! array_key_exists($this->time_slot, $this->availableTimeSlots)) {
            $this->time_slot = '';
        }
    }

    public function updatedBranchId(): void
    {
        $this->branch_id = (string) (MOTBooking::catfordBranchId() ?? $this->branch_id);
        $this->updatedDateOfAppointment();
    }

    public function submit(): void
    {
        $this->validate();

        $customerAuth = Auth::guard('customer')->user();
        $customerProfile = $customerAuth?->customer;
        $customerName = trim((string) ($customerProfile?->first_name.' '.$customerProfile?->last_name));
        $customerName = $customerName !== '' ? $customerName : (string) ($customerAuth?->name ?? 'Portal customer');
        $customerPhone = trim((string) ($customerProfile?->phone ?? $customerAuth?->phone ?? ''));
        if ($customerPhone === '') {
            $this->addError('phone', 'Please add a phone number to your profile before booking an MOT.');

            return;
        }

        $customerEmail = trim((string) ($customerAuth?->email ?? ''));
        $appointmentStart = MOTBooking::appointmentStart($this->date_of_appointment, $this->time_slot);
        $bookingNotes = implode("\n", array_filter([
            'This made by website frontend user.',
            'We are going to edit it later we made the process ahead.',
            'Source: portal.mot.book',
            'Branch: Catford',
            'Registration: '.strtoupper(trim($this->motorbike_reg_no)),
            $this->motorbike_make !== '' ? 'Make: '.trim($this->motorbike_make) : null,
            $this->motorbike_model !== '' ? 'Model: '.trim($this->motorbike_model) : null,
            $this->notes !== '' ? 'Notes: '.trim($this->notes) : null,
        ]));

        $branchId = (int) $this->branch_id;
        $slotTaken = MOTBooking::query()
            ->where('branch_id', $branchId)
            ->whereDate('date_of_appointment', $this->date_of_appointment)
            ->where('start', $appointmentStart->toDateTimeString())
            ->where('status', '!=', MOTBooking::STATUS_CANCELLED)
            ->exists();

        if ($slotTaken) {
            $this->addError('time_slot', 'That time slot has already been reserved.');

            return;
        }

        $serviceBookingId = null;

        DB::transaction(function () use ($appointmentStart, $customerAuth, $customerName, $customerPhone, $customerEmail, $bookingNotes, $branchId, &$serviceBookingId): void {
            MOTBooking::query()->create([
                'branch_id' => $branchId,
                'vehicle_registration' => strtoupper(trim($this->motorbike_reg_no)),
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
                'service_type' => 'MOT portal booking',
                'subject' => 'MOT booking request',
                'description' => implode(' | ', array_filter([
                    'Reg: '.strtoupper(trim($this->motorbike_reg_no)),
                    'Make: '.trim($this->motorbike_make),
                    'Model: '.trim($this->motorbike_model),
                    'Date: '.$this->date_of_appointment,
                    'Time: '.$this->time_slot,
                    $this->notes !== '' ? 'Notes: '.trim($this->notes) : null,
                    'Source: portal.mot.book',
                ])),
                'requires_schedule' => true,
                'booking_date' => $this->date_of_appointment,
                'booking_time' => $this->time_slot,
                'status' => 'Pending',
                'fullname' => $customerName,
                'phone' => $customerPhone,
                'reg_no' => strtoupper(trim($this->motorbike_reg_no)),
                'email' => $customerEmail !== '' ? $customerEmail : null,
            ]);

            $serviceBookingId = $serviceBooking->id;
        });

        $serviceBooking = $serviceBookingId ? ServiceBooking::query()->find($serviceBookingId) : null;

        if ($serviceBooking) {
            app(MailController::class)->sendBookingConfirmation($serviceBooking);
        }

        session()->flash('success', 'MOT booking submitted. We will confirm your appointment shortly.');
        $this->reset([
            'motorbike_reg_no',
            'motorbike_make',
            'motorbike_model',
            'date_of_appointment',
            'time_slot',
            'notes',
        ]);

        $catfordBranchId = MOTBooking::catfordBranchId();
        if ($catfordBranchId) {
            $this->branch_id = (string) $catfordBranchId;
        }
    }

    public function getAvailableTimeSlotsProperty(): array
    {
        if ($this->branch_id === '' || $this->date_of_appointment === '') {
            return $this->timeSlots;
        }

        $branchId = (int) $this->branch_id;
        if ($branchId <= 0) {
            return $this->timeSlots;
        }

        return MOTBooking::availableTimeSlotsForDate($branchId, $this->date_of_appointment);
    }

    public function getActiveCustomerBookingProperty(): ?array
    {
        $customerEmail = trim((string) Auth::guard('customer')->user()?->email);
        if ($customerEmail === '') {
            return null;
        }

        $booking = MOTBooking::query()
            ->with('branch:id,name')
            ->whereRaw('LOWER(customer_email) = ?', [strtolower($customerEmail)])
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
            'date' => $booking->date_of_appointment?->format('d M Y') ?? Carbon::parse($booking->date_of_appointment)->format('d M Y'),
            'time' => BookingSchedule::formatTimeAmPm(Carbon::parse($booking->start ?? $booking->date_of_appointment)->format('H:i')),
            'status' => $booking->status,
            'branch' => $booking->branch?->name ?? 'Catford',
        ];
    }

    public function render()
    {
        return view('livewire.portal.mot.book')
            ->layout('components.layouts.portal', ['title' => 'Book MOT | My Account']);
    }
}
