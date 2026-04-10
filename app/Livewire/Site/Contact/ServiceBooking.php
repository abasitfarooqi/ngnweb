<?php

namespace App\Livewire\Site\Contact;

use App\Http\Controllers\MailController;
use App\Models\Branch;
use App\Models\ServiceBooking as ServiceBookingModel;
use App\Models\SupportConversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceBooking extends Component
{
    public bool $embedded = false;

    public bool $rentalCompactMode = false;

    public bool $repairsEnquiryCompactMode = false;

    /** Portal repair enquiry: compact layout with limited service types + vehicle fields + email + notes (contact from account). */
    public bool $portalRepairsEnquiry = false;

    public ?string $embeddedHeading = null;

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

    public string $notesLabel = 'Additional Notes';

    public string $submitLabel = 'Book Service';

    /** Incremented after a successful submit so the form remounts and Flux widgets reset cleanly. */
    public int $formNonce = 0;

    /**
     * @return list<string>
     */
    public static function allowedServiceTypes(): array
    {
        return [
            'Motorcycle Repairs Enquiry',
            'MOT Booking Enquiry',
            'Motorcycle Full Service Enquiry',
            'Motorcycle Basic Service Enquiry',
            'Motorcycle Rental Enquiry',
            'Accident Management Services Enquiry',
            'Other',
            'Motorcycle Repairs',
            'Motorcycle Full Service',
            'Motorcycle Basic Service',
        ];
    }

    /**
     * @return array<string, string> value => label
     */
    public static function portalRepairsServiceTypeOptions(): array
    {
        return [
            'Motorcycle Repairs Enquiry' => 'Motorcycle repairs enquiry',
            'Motorcycle Full Service Enquiry' => 'Full service',
            'Motorcycle Basic Service Enquiry' => 'Basic service',
            'Other' => 'Other',
        ];
    }

    public function mount(
        bool $embedded = false,
        bool $rentalCompactMode = false,
        bool $repairsEnquiryCompactMode = false,
        bool $portalRepairsEnquiry = false,
        ?string $embeddedHeading = null,
        ?string $initialServiceType = null,
        ?string $initialRegNo = null,
        ?string $initialMake = null,
        ?string $initialModel = null,
        ?string $initialMessage = null
    ): void {
        $this->embedded = $embedded;
        $this->rentalCompactMode = $rentalCompactMode;
        $this->repairsEnquiryCompactMode = $repairsEnquiryCompactMode;
        $this->portalRepairsEnquiry = $portalRepairsEnquiry;
        $this->embeddedHeading = $embeddedHeading;
        $this->branches = Branch::orderBy('name')->get();

        $allowed = self::allowedServiceTypes();

        $preset = request()->query('service');
        if (is_string($preset) && in_array($preset, $allowed, true)) {
            $this->serviceType = $preset;
        }
        if ($initialServiceType !== null && $initialServiceType !== '' && in_array($initialServiceType, $allowed, true)) {
            $this->serviceType = $initialServiceType;
        }

        $this->serviceType = match ($this->serviceType) {
            'Motorcycle Repairs' => 'Motorcycle Repairs Enquiry',
            'Motorcycle Full Service' => 'Motorcycle Full Service Enquiry',
            'Motorcycle Basic Service' => 'Motorcycle Basic Service Enquiry',
            default => $this->serviceType,
        };

        $portalTypes = array_keys(self::portalRepairsServiceTypeOptions());
        if ($this->portalRepairsEnquiry && $this->repairsEnquiryCompactMode && ! in_array($this->serviceType, $portalTypes, true)) {
            $this->serviceType = 'Motorcycle Repairs Enquiry';
        }

        $queryReg = request()->query('reg');
        $queryMake = request()->query('make');
        $queryModel = request()->query('model');
        $queryMessage = request()->query('message');

        $prefillReg = is_string($queryReg) && $queryReg !== '' ? $queryReg : ($initialRegNo ?? '');
        $prefillMake = is_string($queryMake) && $queryMake !== '' ? $queryMake : ($initialMake ?? '');
        $prefillModel = is_string($queryModel) && $queryModel !== '' ? $queryModel : ($initialModel ?? '');
        $prefillMessage = is_string($queryMessage) && $queryMessage !== '' ? $queryMessage : ($initialMessage ?? '');

        if ($prefillReg !== '') {
            $this->regNo = $prefillReg;
        }
        if ($prefillMake !== '') {
            $this->make = $prefillMake;
        }
        if ($prefillModel !== '') {
            $this->model = $prefillModel;
        }
        if ($prefillMessage !== '') {
            $this->message = $prefillMessage;
        }

        if ($this->rentalCompactMode) {
            $this->serviceType = 'Motorcycle Rental Enquiry';
            $this->selectedBranch = '';
            $this->regNo = '';
            $this->make = '';
            $this->model = '';
            $this->notesLabel = 'Notes';
            $this->submitLabel = 'Send rental enquiry';
        }

        if ($this->repairsEnquiryCompactMode) {
            $this->selectedBranch = '';
            if ($this->portalRepairsEnquiry) {
                $customerAuth = Auth::guard('customer')->user();
                if ($customerAuth) {
                    $profile = $customerAuth->customer;
                    $full = trim((string) (($profile?->first_name ?? '').' '.($profile?->last_name ?? '')));
                    if ($full !== '') {
                        $this->name = $full;
                    }
                    if ($customerAuth->email) {
                        $this->email = (string) $customerAuth->email;
                    }
                    if ($profile?->phone) {
                        $this->phone = (string) $profile->phone;
                    }
                }
                $this->notesLabel = 'Message / additional notes';
            } else {
                $this->serviceType = 'Motorcycle Repairs Enquiry';
                $this->regNo = '';
                $this->make = '';
                $this->model = '';
                $this->notesLabel = 'Describe your repair or service need';
            }
            $this->submitLabel = 'Send repair enquiry';
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

        if ($this->rentalCompactMode) {
            $this->serviceType = 'Motorcycle Rental Enquiry';
        }
        if ($this->repairsEnquiryCompactMode && ! $this->portalRepairsEnquiry) {
            $this->serviceType = 'Motorcycle Repairs Enquiry';
        }

        $branch = $this->branches->firstWhere('id', (int) ($validated['selectedBranch'] ?: 0));
        $serviceTypeLabel = (string) $validated['serviceType'];

        $descriptionBits = array_filter([
            $branch ? 'Branch: '.$branch->name : null,
            'Reg: '.($this->regNo ?: 'N/A'),
            'Make: '.($this->make ?: 'N/A'),
            'Model: '.($this->model ?: 'N/A'),
            'Message: '.($this->message ?: 'N/A'),
        ]);

        $customerAuth = Auth::guard('customer')->user();
        $customerProfile = $customerAuth?->customer;
        $resolvedEmail = trim((string) ($validated['email'] ?: ($customerAuth?->email ?? '')));
        $resolvedPhone = trim((string) ($validated['phone'] ?: ($customerProfile?->phone ?? '')));
        $resolvedName = trim((string) ($validated['name'] ?: (($customerProfile?->first_name ?? '').' '.($customerProfile?->last_name ?? ''))));
        $resolvedName = $resolvedName !== '' ? $resolvedName : 'Portal customer';
        $ownershipCustomerId = $customerAuth?->customer_id ?: $customerProfile?->id;
        $conversation = null;
        if ($customerAuth) {
            $conversation = SupportConversation::query()->create([
                'customer_auth_id' => $customerAuth->id,
                'title' => $serviceTypeLabel,
                'topic' => $serviceTypeLabel,
                'status' => 'open',
            ]);
        }

        $booking = ServiceBookingModel::create([
            'customer_id' => $ownershipCustomerId,
            'customer_auth_id' => $customerAuth?->id,
            'conversation_id' => $conversation?->id,
            'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
            'enquiry_type' => ServiceBookingModel::inferEnquiryType($serviceTypeLabel, implode(' | ', $descriptionBits)),
            'service_type' => $serviceTypeLabel,
            'subject' => $serviceTypeLabel,
            'description' => implode(' | ', $descriptionBits),
            'requires_schedule' => $this->requiresScheduleSelection,
            'booking_date' => $validated['preferredDate'] ?: null,
            'booking_time' => $validated['preferredTime'] ?: null,
            'status' => 'Pending',
            'fullname' => $resolvedName,
            'phone' => $resolvedPhone,
            'reg_no' => $this->regNo ?: 'N/A',
            'email' => $resolvedEmail !== '' ? $resolvedEmail : null,
        ]);

        if ($conversation) {
            $conversation->forceFill([
                'service_booking_id' => $booking->id,
            ])->save();
        }

        app(MailController::class)->sendBookingConfirmation($booking);

        $successMessage = 'Service booking request received. We will confirm your appointment shortly.';
        if ($this->repairsEnquiryCompactMode) {
            $successMessage = 'Your repair enquiry has been sent. We will contact you shortly.';
        } elseif ($this->rentalCompactMode) {
            $successMessage = 'Your rental enquiry has been sent. We will contact you shortly.';
        }
        session()->flash('success', $successMessage);

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
        $view = view('livewire.site.contact.service-booking');

        if ($this->embedded) {
            return $view;
        }

        return $view->layout('components.layouts.public', [
            'title' => 'Book a Service Enquiry | NGN Motors',
            'description' => 'Book a motorcycle service at NGN Motors London.',
        ]);
    }

    private function rules(): array
    {
        $portalContactFromProfile = $this->portalRepairsEnquiry
            && $this->repairsEnquiryCompactMode
            && Auth::guard('customer')->check();

        $serviceRule = ['required', 'string'];
        if ($this->portalRepairsEnquiry && $this->repairsEnquiryCompactMode) {
            $serviceRule[] = Rule::in(array_keys(self::portalRepairsServiceTypeOptions()));
        } else {
            $serviceRule[] = Rule::in(self::allowedServiceTypes());
        }

        return [
            'name' => array_values(array_filter([
                $portalContactFromProfile ? 'nullable' : 'required',
                'string',
                'min:2',
            ])),
            'email' => ['required', 'email'],
            'phone' => array_values(array_filter([
                $portalContactFromProfile ? 'nullable' : 'required',
                'string',
                $portalContactFromProfile ? null : 'min:10',
            ])),
            'selectedBranch' => [
                'nullable',
                ...(($this->rentalCompactMode || $this->repairsEnquiryCompactMode) ? [] : [Rule::exists('branches', 'id')]),
            ],
            'serviceType' => $serviceRule,
            'preferredDate' => [$this->requiresScheduleSelection ? 'required' : 'nullable', 'date', 'after_or_equal:today'],
            'preferredTime' => [$this->requiresScheduleSelection ? 'required' : 'nullable', 'date_format:H:i'],
            'cookiePolicy' => [($this->rentalCompactMode || $this->repairsEnquiryCompactMode) ? 'nullable' : 'accepted'],
        ];
    }
}
