<?php

namespace App\Livewire\Portal\Repairs;

use App\Models\Branch;
use App\Models\MotorbikeRepair;
use App\Models\ServiceBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Request extends Component
{
    public string $service_type = '';

    public string $bike_reg_no = '';

    public string $bike_make = '';

    public string $bike_model = '';

    public string $mileage = '';

    public string $issue_description = '';

    public bool $needs_collection = false;

    public string $collection_postcode = '';

    public string $collection_address = '';

    public string $date_requested = '';

    public string $time_slot = '';

    public string $branch_id = '';

    public string $repair_authorisation_limit = '0';

    public array $timeSlots = [
        '09:00' => '09:00 AM', '09:30' => '09:30 AM', '10:00' => '10:00 AM',
        '10:30' => '10:30 AM', '11:00' => '11:00 AM', '11:30' => '11:30 AM',
        '12:00' => '12:00 PM', '13:00' => '01:00 PM', '13:30' => '01:30 PM',
        '14:00' => '02:00 PM', '14:30' => '02:30 PM', '15:00' => '03:00 PM',
        '15:30' => '03:30 PM', '16:00' => '04:00 PM', '16:30' => '04:30 PM',
    ];

    protected function rules(): array
    {
        $rules = [
            'service_type' => 'required|string',
            'bike_reg_no' => 'required|string|min:2|max:12',
            'issue_description' => 'nullable|string',
            'date_requested' => 'required|date|after:today',
            'time_slot' => 'required',
            'branch_id' => 'required|exists:branches,id',
        ];

        return $rules;
    }

    public function mount()
    {
        $profile = Auth::guard('customer')->user()->customer;
        if ($profile && $profile->preferred_branch_id) {
            $this->branch_id = (string) $profile->preferred_branch_id;
        }
    }

    public function submit()
    {
        $this->validate();

        $customerAuth = Auth::guard('customer')->user();
        $customerProfile = $customerAuth?->customer;
        $customerName = trim((string) ($customerProfile?->first_name.' '.$customerProfile?->last_name));
        $customerName = $customerName !== '' ? $customerName : (string) ($customerAuth?->name ?? 'Portal customer');
        $customerPhone = trim((string) ($customerProfile?->phone ?? $customerAuth?->phone ?? ''));
        $customerEmail = trim((string) ($customerAuth?->email ?? ''));

        MotorbikeRepair::query()->create([
            'arrival_date' => trim($this->date_requested.' '.$this->time_slot),
            'motorbike_id' => null,
            'notes' => implode("\n", array_filter([
                'Service type: '.trim($this->service_type),
                'Registration: '.strtoupper(trim($this->bike_reg_no)),
                $this->bike_make ? 'Make: '.trim($this->bike_make) : null,
                $this->bike_model ? 'Model: '.trim($this->bike_model) : null,
                $this->mileage ? 'Mileage: '.trim($this->mileage) : null,
                $this->issue_description ? 'Issue: '.trim($this->issue_description) : null,
                'Authorisation limit: '.trim($this->repair_authorisation_limit),
                'Source: portal.repairs.request',
            ])),
            'is_repaired' => false,
            'is_returned' => false,
            'fullname' => $customerName,
            'email' => $customerEmail ?: null,
            'phone' => $customerPhone ?: null,
            'branch_id' => (int) $this->branch_id,
            'user_id' => null,
        ]);

        ServiceBooking::query()->create([
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => 'authenticated_customer',
            'enquiry_type' => 'service',
            'service_type' => 'Repairs portal booking',
            'subject' => 'Repair booking request',
            'description' => implode(' | ', array_filter([
                'Service type: '.trim($this->service_type),
                'Reg: '.strtoupper(trim($this->bike_reg_no)),
                $this->bike_make ? 'Make: '.trim($this->bike_make) : null,
                $this->bike_model ? 'Model: '.trim($this->bike_model) : null,
                $this->mileage ? 'Mileage: '.trim($this->mileage) : null,
                $this->issue_description ? 'Issue: '.trim($this->issue_description) : null,
                'Date: '.$this->date_requested,
                'Time: '.$this->time_slot,
                'Authorisation limit: '.trim($this->repair_authorisation_limit),
                'Source: portal.repairs.request',
            ])),
            'requires_schedule' => true,
            'booking_date' => $this->date_requested,
            'booking_time' => $this->time_slot,
            'status' => 'Pending',
            'fullname' => $customerName,
            'phone' => $customerPhone,
            'reg_no' => strtoupper(trim($this->bike_reg_no)),
            'email' => $customerEmail ?: null,
        ]);

        if ($customerEmail !== '') {
            Mail::raw(
                "Your repairs booking request has been received.\n\nRegistration: ".strtoupper(trim($this->bike_reg_no))."\nDate: {$this->date_requested}\nTime: {$this->time_slot}\n\nWe will confirm your booking shortly.",
                function ($message) use ($customerEmail): void {
                    $message
                        ->to($customerEmail)
                        ->subject('Repairs booking request received');
                }
            );
        }

        session()->flash('success', 'Repair booking submitted. We will confirm your appointment shortly.');
        $this->reset([
            'service_type', 'bike_reg_no', 'bike_make', 'bike_model', 'mileage',
            'issue_description', 'needs_collection', 'collection_postcode', 'collection_address',
            'date_requested', 'time_slot',
        ]);
        $this->needs_collection = false;
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();

        return view('livewire.portal.repairs.request', compact('branches'))
            ->layout('components.layouts.portal', ['title' => 'Book Repairs | My Account']);
    }
}
