<?php

namespace App\Livewire\Portal\Repairs;

use App\Mail\CustomerAppointmentNotification;
use App\Models\Branch;
use App\Models\CustomerAppointments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Appointment extends Component
{
    public const BOOKING_REASON_PREFIX = 'Repairs workshop appointment';

    public string $service_type = '';

    public string $bike_reg_no = '';

    public string $bike_make = '';

    public string $bike_model = '';

    public string $mileage = '';

    public string $issue_description = '';

    public string $date_requested = '';

    public string $time_slot = '';

    public string $branch_id = '';

    public string $repair_authorisation_limit = '0';

    /** @var array<string, string> */
    public array $timeSlots = [
        '09:00' => '09:00', '09:30' => '09:30', '10:00' => '10:00',
        '10:30' => '10:30', '11:00' => '11:00', '11:30' => '11:30',
        '12:00' => '12:00', '13:00' => '13:00', '13:30' => '13:30',
        '14:00' => '14:00', '14:30' => '14:30', '15:00' => '15:00',
        '15:30' => '15:30', '16:00' => '16:00', '16:30' => '16:30',
    ];

    protected function rules(): array
    {
        return [
            'service_type' => 'required|string',
            'bike_reg_no' => 'required|string|min:2|max:12',
            'date_requested' => 'required|date|after:today',
            'time_slot' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    public function mount(): void
    {
        $profile = Auth::guard('customer')->user()?->customer;
        if ($profile && $profile->preferred_branch_id) {
            $this->branch_id = (string) $profile->preferred_branch_id;
        }
    }

    public function submit(): void
    {
        $this->validate();

        $customerAuth = Auth::guard('customer')->user();
        $customerProfile = $customerAuth?->customer;
        $customerName = trim((string) ($customerProfile?->first_name.' '.$customerProfile?->last_name));
        $customerName = $customerName !== '' ? $customerName : (string) ($customerAuth?->name ?? 'Portal customer');
        $customerPhone = trim((string) ($customerProfile?->phone ?? ''));
        $customerEmail = trim((string) ($customerAuth?->email ?? ''));

        $branch = Branch::query()->find((int) $this->branch_id);
        $branchLabel = $branch?->name ?? 'Branch #'.$this->branch_id;

        $appointmentAt = $this->date_requested.' '.$this->time_slot.':00';

        $bookingReason = implode("\n", array_filter([
            self::BOOKING_REASON_PREFIX,
            'Source: portal.repairs.appointment',
            'Service type: '.trim($this->service_type),
            'Branch: '.$branchLabel,
            $this->bike_make !== '' ? 'Make: '.trim($this->bike_make) : null,
            $this->bike_model !== '' ? 'Model: '.trim($this->bike_model) : null,
            $this->mileage !== '' ? 'Mileage: '.trim($this->mileage) : null,
            $this->issue_description !== '' ? 'Issue: '.trim($this->issue_description) : null,
            'Repair authorisation limit: '.trim($this->repair_authorisation_limit),
        ]));

        $appointment = CustomerAppointments::query()->create([
            'appointment_date' => $appointmentAt,
            'customer_name' => $customerName,
            'registration_number' => strtoupper(trim($this->bike_reg_no)),
            'contact_number' => $customerPhone !== '' ? $customerPhone : null,
            'email' => $customerEmail !== '' ? $customerEmail : null,
            'is_resolved' => false,
            'booking_reason' => $bookingReason,
        ]);

        $recipients = array_values(array_filter(array_unique([
            $customerEmail !== '' ? $customerEmail : null,
            'customerservice@neguinhomotors.co.uk',
        ])));

        if ($recipients !== []) {
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
                \Log::error('Repairs appointment email failed', ['error' => $e->getMessage()]);
            }
        }

        session()->flash('success', 'Your repairs appointment request has been recorded. We will confirm by email or phone.');

        $this->reset([
            'service_type', 'bike_reg_no', 'bike_make', 'bike_model', 'mileage',
            'issue_description', 'date_requested', 'time_slot',
        ]);
        if ($customerProfile?->preferred_branch_id) {
            $this->branch_id = (string) $customerProfile->preferred_branch_id;
        } else {
            $this->branch_id = '';
        }
        $this->repair_authorisation_limit = '0';
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();

        return view('livewire.portal.repairs.appointment', compact('branches'))
            ->layout('components.layouts.portal', ['title' => 'Repairs Appointment | My Account']);
    }
}
