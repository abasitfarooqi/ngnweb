<?php

namespace App\Livewire\Portal\MOT;

use App\Models\Branch;
use App\Models\MOTBooking;
use App\Models\ServiceBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

    public array $timeSlots = [
        '09:00' => '09:00 AM',
        '09:30' => '09:30 AM',
        '10:00' => '10:00 AM',
        '10:30' => '10:30 AM',
        '11:00' => '11:00 AM',
        '11:30' => '11:30 AM',
        '12:00' => '12:00 PM',
        '13:00' => '01:00 PM',
        '13:30' => '01:30 PM',
        '14:00' => '02:00 PM',
        '14:30' => '02:30 PM',
        '15:00' => '03:00 PM',
        '15:30' => '03:30 PM',
        '16:00' => '04:00 PM',
        '16:30' => '04:30 PM',
    ];

    protected $rules = [
        'branch_id' => 'required|exists:branches,id',
        'motorbike_reg_no' => 'required|string|min:2|max:10',
        'motorbike_make' => 'required|string|min:2|max:50',
        'motorbike_model' => 'required|string|min:2|max:50',
        'date_of_appointment' => 'required|date|after:today',
        'time_slot' => 'required',
        'notes' => 'nullable|string|max:2000',
    ];

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
        $appointmentStart = trim($this->date_of_appointment.' '.$this->time_slot);

        MOTBooking::withoutEvents(function () use ($appointmentStart, $customerEmail, $customerName, $customerPhone): void {
            MOTBooking::query()->create([
                'branch_id' => (int) $this->branch_id,
                'vehicle_registration' => strtoupper(trim($this->motorbike_reg_no)),
                'vehicle_chassis' => null,
                'vehicle_color' => null,
                'date_of_appointment' => $this->date_of_appointment,
                'start' => $appointmentStart,
                'end' => $appointmentStart,
                'customer_name' => $customerName,
                'customer_contact' => $customerPhone,
                'customer_email' => $customerEmail ?: null,
                'status' => 'pending',
                'title' => 'pending MOT '.strtoupper(trim($this->motorbike_reg_no)).' '.$customerName,
                'notes' => implode("\n", array_filter([
                    'Make: '.trim($this->motorbike_make),
                    'Model: '.trim($this->motorbike_model),
                    $this->notes ? 'Notes: '.trim($this->notes) : null,
                    'Source: portal.mot.book',
                ])),
                'all_day' => false,
                'is_validate' => true,
                'payment_method' => null,
                'payment_notes' => null,
                'user_id' => null,
            ]);
        });

        ServiceBooking::query()->create([
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => 'authenticated_customer',
            'enquiry_type' => 'mot',
            'service_type' => 'MOT portal booking',
            'subject' => 'MOT booking request',
            'description' => implode(' | ', array_filter([
                'Reg: '.strtoupper(trim($this->motorbike_reg_no)),
                'Make: '.trim($this->motorbike_make),
                'Model: '.trim($this->motorbike_model),
                'Date: '.$this->date_of_appointment,
                'Time: '.$this->time_slot,
                $this->notes ? 'Notes: '.trim($this->notes) : null,
                'Source: portal.mot.book',
            ])),
            'requires_schedule' => true,
            'booking_date' => $this->date_of_appointment,
            'booking_time' => $this->time_slot,
            'status' => 'Pending',
            'fullname' => $customerName,
            'phone' => $customerPhone,
            'reg_no' => strtoupper(trim($this->motorbike_reg_no)),
            'email' => $customerEmail ?: null,
        ]);

        if ($customerEmail !== '') {
            $branchName = optional(Branch::query()->find((int) $this->branch_id))->name ?? 'Selected branch';
            $timeLabel = $this->timeSlots[$this->time_slot] ?? $this->time_slot;
            $mailPayload = [
                'subject' => 'MOT booking request received - NGN Motors',
                'heading' => 'MOT booking request received',
                'greetingName' => $customerName,
                'introLines' => [
                    'We have received your MOT booking request from your NGN account.',
                    'A member of our team will contact you shortly to confirm your appointment.',
                ],
                'details' => [
                    'Branch' => $branchName,
                    'Registration' => strtoupper(trim($this->motorbike_reg_no)),
                    'Vehicle' => trim($this->motorbike_make.' '.$this->motorbike_model),
                    'Preferred Date' => $this->date_of_appointment,
                    'Preferred Time' => $timeLabel,
                    'Phone' => $customerPhone ?: 'N/A',
                    'Email' => $customerEmail,
                    'Notes' => trim($this->notes) !== '' ? trim($this->notes) : 'N/A',
                ],
                'outroLines' => [
                    'Please reply to this email if you need to amend your preferred date or time.',
                ],
            ];

            try {
                Mail::send('emails.templates.universal', $mailPayload, function ($message) use ($customerEmail, $customerName): void {
                    $message
                        ->to($customerEmail, $customerName)
                        ->subject('MOT booking request received - NGN Motors');
                });
            } catch (\Throwable $e) {
                report($e);
            }
        }

        session()->flash('success', 'MOT booking submitted. We will confirm your appointment shortly.');
        $this->reset(['motorbike_reg_no', 'motorbike_make', 'motorbike_model', 'date_of_appointment', 'time_slot', 'notes']);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();

        return view('livewire.portal.mot.book', compact('branches'))
            ->layout('components.layouts.portal', ['title' => 'Book MOT | My Account']);
    }
}
