<?php

namespace App\Livewire\Site\Contact;

use App\Http\Controllers\MailController;
use App\Models\Branch;
use App\Models\MOTBooking;
use App\Models\ServiceBooking as ServiceBookingModel;
use App\Models\SupportConversation;
use App\Rules\BookableTimeSlot;
use App\Rules\NotSunday;
use App\Support\BookingSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Public service-booking dropdown (site + all-services enquiry).
     *
     * @return list<string>
     */
    public static function publicServiceTypeOptions(): array
    {
        return [
            'Motorcycle Repairs Enquiry',
            'Motorcycle Engine Repairs Enquiry',
            'MOT Booking Enquiry',
            'Motorcycle Full Service Enquiry',
            'Motorcycle Basic Service Enquiry',
            'Motorcycle Rental Enquiry',
            'Accident Management Services Enquiry',
            'Other',
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedServiceTypes(): array
    {
        return array_merge(self::publicServiceTypeOptions(), [
            'Motorcycle Repairs',
            'Motorcycle Full Service',
            'Motorcycle Basic Service',
        ]);
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

        if ($this->serviceType === 'MOT Booking Enquiry') {
            $this->selectedBranch = (string) (MOTBooking::catfordBranchId() ?? $this->selectedBranch);
        }

        $this->applyServiceTypePresentation();
    }

    private function applyServiceTypePresentation(): void
    {
        if ($this->serviceType === 'Motorcycle Engine Repairs Enquiry') {
            $this->submitLabel = 'Send engine rebuild enquiry';
            $this->notesLabel = 'Describe the engine issue';
        }
    }

    public function updatedServiceType(?string $value): void
    {
        $this->applyServiceTypePresentation();

        if ((string) $value === 'MOT Booking Enquiry') {
            $this->selectedBranch = (string) (MOTBooking::catfordBranchId() ?? $this->selectedBranch);
            if ($this->preferredTime !== '' && ! array_key_exists($this->preferredTime, $this->availableTimeSlots)) {
                $this->preferredTime = '';
            }
        } elseif (! in_array((string) $value, [
            'MOT Booking Enquiry',
            'Accident Management Services Enquiry',
        ], true)) {
            $this->preferredDate = '';
            $this->preferredTime = '';
            $this->resetValidation(['preferredDate', 'preferredTime']);
        }
    }

    public function updatedPreferredDate(): void
    {
        if ($this->preferredTime !== '' && ! array_key_exists($this->preferredTime, $this->availableTimeSlots)) {
            $this->preferredTime = '';
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
        $customerAuth = Auth::guard('customer')->user();
        $customerProfile = $customerAuth?->customer;
        $resolvedEmail = trim((string) ($validated['email'] ?: ($customerAuth?->email ?? '')));
        $resolvedPhone = trim((string) ($validated['phone'] ?: ($customerProfile?->phone ?? '')));
        $resolvedName = trim((string) ($validated['name'] ?: (($customerProfile?->first_name ?? '').' '.($customerProfile?->last_name ?? ''))));
        $resolvedName = $resolvedName !== '' ? $resolvedName : 'Portal customer';
        $ownershipCustomerId = $customerAuth?->customer_id ?: $customerProfile?->id;

        $isMotBooking = $serviceTypeLabel === 'MOT Booking Enquiry';
        $motBookingId = null;
        $serviceBooking = null;
        $slotTaken = false;

        if ($isMotBooking) {
            $branchId = (int) (MOTBooking::catfordBranchId() ?? $validated['selectedBranch']);
            $appointmentStart = MOTBooking::appointmentStart($validated['preferredDate'], $validated['preferredTime']);
            $slotTaken = MOTBooking::query()
                ->where('branch_id', $branchId)
                ->whereDate('date_of_appointment', $validated['preferredDate'])
                ->where('start', $appointmentStart->toDateTimeString())
                ->where('status', '!=', MOTBooking::STATUS_CANCELLED)
                ->exists();

            if ($slotTaken) {
                $this->addError('preferredTime', 'That time slot has already been reserved.');

                return;
            }
        }

        $descriptionBits = array_filter([
            $branch ? 'Branch: '.$branch->name : null,
            'Reg: '.($this->regNo ?: 'N/A'),
            'Make: '.($this->make ?: 'N/A'),
            'Model: '.($this->model ?: 'N/A'),
            'Message: '.($this->message ?: 'N/A'),
        ]);

        $conversation = null;
        if ($customerAuth) {
            $conversation = SupportConversation::query()->create([
                'customer_auth_id' => $customerAuth->id,
                'title' => $serviceTypeLabel,
                'topic' => $serviceTypeLabel,
                'status' => 'open',
            ]);
        }

        DB::transaction(function () use (
            $isMotBooking,
            $customerAuth,
            $conversation,
            $ownershipCustomerId,
            $serviceTypeLabel,
            $descriptionBits,
            $resolvedName,
            $resolvedPhone,
            $resolvedEmail,
            $validated,
            &$serviceBooking,
            &$motBookingId
        ): void {
            if ($isMotBooking) {
                $appointmentStart = MOTBooking::appointmentStart($validated['preferredDate'], $validated['preferredTime']);
                $motBooking = MOTBooking::query()->create([
                    'branch_id' => (int) (MOTBooking::catfordBranchId() ?? $validated['selectedBranch']),
                    'vehicle_registration' => strtoupper(trim($this->regNo)),
                    'vehicle_chassis' => null,
                    'vehicle_color' => null,
                    'date_of_appointment' => $appointmentStart->toDateTimeString(),
                    'start' => $appointmentStart->toDateTimeString(),
                    'end' => $appointmentStart->toDateTimeString(),
                    'customer_name' => $resolvedName,
                    'customer_contact' => $resolvedPhone,
                    'customer_email' => $resolvedEmail !== '' ? $resolvedEmail : null,
                    'status' => MOTBooking::STATUS_PENDING,
                    'title' => null,
                    'notes' => implode("\n", array_filter([
                        'This made by website frontend user.',
                        'We are going to edit it later we made the process ahead.',
                        'Source: site.contact.service-booking',
                        'Branch: Catford',
                        'Registration: '.strtoupper(trim($this->regNo)),
                        $this->make !== '' ? 'Make: '.trim($this->make) : null,
                        $this->model !== '' ? 'Model: '.trim($this->model) : null,
                        $this->message !== '' ? 'Notes: '.trim($this->message) : null,
                    ])),
                    'all_day' => false,
                    'is_validate' => true,
                    'is_paid' => false,
                    'payment_method' => null,
                    'payment_notes' => null,
                    'user_id' => null,
                ]);

                $motBookingId = $motBooking->id;
            }

            $serviceBooking = ServiceBookingModel::create([
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
        });

        if ($conversation && $serviceBooking) {
            $conversation->forceFill([
                'service_booking_id' => $serviceBooking->id,
            ])->save();
        }

        app(MailController::class)->sendBookingConfirmation($serviceBooking);

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

    public function getAvailableTimeSlotsProperty(): array
    {
        if ($this->serviceType === 'MOT Booking Enquiry') {
            if ($this->selectedBranch === '' || $this->preferredDate === '') {
                return MOTBooking::motTimeSlots();
            }

            return MOTBooking::availableTimeSlotsForDate((int) $this->selectedBranch, $this->preferredDate);
        }

        if ($this->serviceType === 'Accident Management Services Enquiry') {
            if ($this->preferredDate === '') {
                return MOTBooking::accidentManagementTimeSlots();
            }

            return MOTBooking::availableAccidentManagementTimeSlotsForDate($this->preferredDate);
        }

        return MOTBooking::accidentManagementTimeSlots();
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
            'selectedBranch' => array_values(array_filter([
                ($this->serviceType === 'MOT Booking Enquiry') ? 'required' : (($this->rentalCompactMode || $this->repairsEnquiryCompactMode) ? 'nullable' : 'nullable'),
                'integer',
                ($this->serviceType === 'MOT Booking Enquiry' || (! $this->rentalCompactMode && ! $this->repairsEnquiryCompactMode)) ? Rule::exists('branches', 'id') : null,
                $this->serviceType === 'MOT Booking Enquiry' && MOTBooking::catfordBranchId() ? Rule::in([MOTBooking::catfordBranchId(), (string) MOTBooking::catfordBranchId()]) : null,
            ])),
            'serviceType' => $serviceRule,
            'preferredDate' => array_values(array_filter([
                $this->requiresScheduleSelection ? 'required' : 'nullable',
                'date',
                'after_or_equal:today',
                new NotSunday,
            ])),
            'preferredTime' => array_values(array_filter([
                $this->requiresScheduleSelection ? 'required' : 'nullable',
                'date_format:H:i',
                Rule::in(array_keys($this->availableTimeSlots)),
                $this->requiresScheduleSelection ? new BookableTimeSlot($this->preferredDate) : null,
            ])),
            'cookiePolicy' => [($this->rentalCompactMode || $this->repairsEnquiryCompactMode) ? 'nullable' : 'accepted'],
        ];
    }
}
