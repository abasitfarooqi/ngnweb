<?php

namespace App\Livewire\Site\Contact;

use App\Http\Controllers\MailController;
use App\Models\Branch;
use App\Models\ServiceBooking as ServiceBookingModel;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceBooking extends Component
{
    public $branches;

    public $name = '';

    public $email = '';

    public $phone = '';

    public $selectedBranch = '';

    public $serviceType = '';

    public $regNo = '';

    public $make = '';

    public $model = '';

    public $preferredDate = '';

    public $preferredTime = '';

    public $message = '';

    public bool $cookiePolicy = false;

    /** Incremented after a successful submit so the form remounts and Flux widgets reset cleanly. */
    public int $formNonce = 0;

    public function mount(): void
    {
        $this->branches = Branch::orderBy('name')->get();

        $preset = request()->query('service');
        $allowed = [
            'Accident Management Services Enquiry',
            'MOT Booking Enquiry',
            'Motorcycle Repairs',
            'Motorcycle Full Service',
            'Motorcycle Basic Service',
            'Motorcycle Rental Enquiry',
            'Other',
        ];
        if (is_string($preset) && in_array($preset, $allowed, true)) {
            $this->serviceType = $preset;
        }
    }

    public function updatedServiceType(?string $value): void
    {
        if (! in_array((string) $value, [
            'MOT Booking Enquiry',
            'Accident Management Services Enquiry',
        ], true)) {
            $this->preferredDate = '';
            $this->preferredTime = '';
            $this->resetValidation(['preferredDate', 'preferredTime']);
        }
    }

    public function submitBooking(): void
    {
        $validated = $this->validate($this->rules());

        $branch = $this->branches->firstWhere('id', (int) ($validated['selectedBranch'] ?: 0));
        $serviceTypeLabel = (string) $validated['serviceType'];

        $descriptionBits = array_filter([
            $branch ? 'Branch: '.$branch->name : null,
            'Make: '.($this->make ?: 'N/A'),
            'Model: '.($this->model ?: 'N/A'),
            'Message: '.($this->message ?: 'N/A'),
        ]);

        $booking = ServiceBookingModel::create([
            'service_type' => $serviceTypeLabel,
            'description' => implode(' | ', $descriptionBits),
            'requires_schedule' => $this->requiresScheduleSelection,
            'booking_date' => $validated['preferredDate'] ?: null,
            'booking_time' => $validated['preferredTime'] ?: null,
            'status' => 'Pending',
            'fullname' => $validated['name'],
            'phone' => $validated['phone'],
            'reg_no' => $this->regNo ?: 'N/A',
            'email' => $validated['email'],
        ]);

        app(MailController::class)->sendBookingConfirmation($booking);

        session()->flash('success', 'Service booking request received. We will confirm your appointment shortly.');

        $this->resetValidation();
        $this->reset([
            'name',
            'email',
            'phone',
            'selectedBranch',
            'serviceType',
            'regNo',
            'make',
            'model',
            'preferredDate',
            'preferredTime',
            'message',
            'cookiePolicy',
        ]);
        $this->formNonce++;
    }

    public function getRequiresScheduleSelectionProperty(): bool
    {
        return in_array($this->serviceType, [
            'MOT Booking Enquiry',
            'Accident Management Services Enquiry',
        ], true);
    }

    public function render()
    {
        return view('livewire.site.contact.service-booking')
            ->layout('components.layouts.public', [
                'title' => 'Book a Service Enquiry | NGN Motors',
                'description' => 'Book a motorcycle service at NGN Motors London.',
            ]);
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'selectedBranch' => ['nullable', Rule::exists('branches', 'id')],
            'serviceType' => ['required', 'string'],
            'preferredDate' => [$this->requiresScheduleSelection ? 'required' : 'nullable', 'date', 'after_or_equal:today'],
            'preferredTime' => [$this->requiresScheduleSelection ? 'required' : 'nullable', 'date_format:H:i'],
            'cookiePolicy' => ['accepted'],
        ];
    }
}
