<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Support\AdminDateTimeInput;
use App\Support\DocumentUploadAccessGenerator;
use App\Support\RentalIntakeDraft;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use RuntimeException;

#[Layout('flux-admin.layouts.app')]
#[Title('New booking — Flux Admin')]
class NewBookingWizard extends Component
{
    use WithAuthorization;

    public int $step = 1;

    public const MAX_STEP = 6;

    public string $bikeSearch = '';

    public ?int $motorbikeId = null;

    public ?float $weeklyRent = null;

    public string $customerSearch = '';

    public ?int $customerId = null;

    public string $startDate;

    public float $deposit = 0;

    public float $initialPayment = 0;

    public string $paymentMethod = 'cash';

    public bool $termsAccepted = false;

    public string $notes = '';

    public bool $sendDocUploadLink = true;

    public ?string $docUploadLink = null;

    #[Url(as: 'draft', except: null)]
    public ?int $draftBookingId = null;

    public ?int $createdBookingId = null;

    public ?int $resumableDraftId = null;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->startDate = AdminDateTimeInput::toLocal(now());

        if ($this->draftBookingId) {
            $this->loadDraftState($this->draftBookingId, announce: true);

            return;
        }

        $resumable = app(RentalIntakeDraft::class)->findResumableForUser((int) auth()->id());
        $this->resumableDraftId = $resumable?->id;

        if (session()->has('flux_rental_intake_bike_id')) {
            $this->motorbikeId = (int) session('flux_rental_intake_bike_id');
            $rent = session('flux_rental_intake_weekly_rent');
            $this->weeklyRent = $rent !== null && (float) $rent > 0 ? (float) $rent : null;
            $this->step = max($this->step, 2);
        }
    }

    public function resumeDraft(int $bookingId): void
    {
        $this->draftBookingId = $bookingId;
        $this->loadDraftState($bookingId, announce: true);
    }

    private function loadDraftState(int $bookingId, bool $announce = false): void
    {
        try {
            $state = app(RentalIntakeDraft::class)->load($bookingId, (int) auth()->id());
        } catch (RuntimeException $e) {
            $this->draftBookingId = null;
            if ($announce) {
                session()->flash('status', $e->getMessage());
            }
            $this->resumableDraftId = null;

            return;
        }

        $this->createdBookingId = $bookingId;
        $this->motorbikeId = $state['motorbikeId'];
        $this->customerId = $state['customerId'];
        $this->weeklyRent = $state['weeklyRent'] > 0 ? $state['weeklyRent'] : null;
        $this->deposit = $state['deposit'];
        $this->startDate = $state['startDate'];
        $this->notes = $state['notes'];
        $this->termsAccepted = $state['termsAccepted'];
        $this->paymentMethod = $state['paymentMethod'];
        $this->initialPayment = $state['initialPayment'];
        $this->sendDocUploadLink = $state['sendDocUploadLink'];

        $loadedStep = (int) $state['step'];
        if ($loadedStep >= self::MAX_STEP) {
            $this->step = self::MAX_STEP;
            if ($this->sendDocUploadLink) {
                $this->docUploadLink = $this->buildCustomerUploadLink($bookingId);
            }
        } else {
            $this->step = max(2, min($loadedStep, self::MAX_STEP - 1));
            if ($this->customerId && $this->motorbikeId && $this->step < 3) {
                $this->step = 3;
            }
        }

        $this->resumableDraftId = null;

        if ($announce) {
            session()->flash('status', 'Resumed intake draft #'.$bookingId.'.');
        }
    }

    public function discardDraft(): void
    {
        $id = $this->draftBookingId ?? $this->resumableDraftId;

        if (! $id) {
            session()->forget(['flux_rental_intake_bike_id', 'flux_rental_intake_weekly_rent']);
            $this->resetWizard();
            $this->redirectRoute('flux-admin.new-booking.index', navigate: true);

            return;
        }

        try {
            app(RentalIntakeDraft::class)->discard($id, (int) auth()->id());
        } catch (RuntimeException $e) {
            $this->addError('draft', $e->getMessage());

            return;
        }

        session()->forget(['flux_rental_intake_bike_id', 'flux_rental_intake_weekly_rent']);
        $this->resetWizard();
        session()->flash('status', 'Draft discarded. You can start a new intake.');
        $this->redirectRoute('flux-admin.new-booking.index', navigate: true);
    }

    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > self::MAX_STEP) {
            return;
        }
        if ($step >= 2 && ! $this->motorbikeId) {
            $this->addError('motorbikeId', 'Select a motorbike first.');

            return;
        }
        if ($step >= 3 && ! $this->customerId) {
            $this->addError('customerId', 'Select a customer first.');

            return;
        }
        if ($step >= 4 && ! $this->termsAccepted) {
            $this->addError('termsAccepted', 'Agree to the terms first.');

            return;
        }
        if ($step >= 5 && (! $this->weeklyRent || $this->weeklyRent <= 0)) {
            $this->addError('weeklyRent', 'Set the weekly rent on the payment step first.');

            return;
        }
        $this->resetErrorBag();
        $this->step = $step;
    }

    public function selectMotorbike(int $id, ?float $rent = null): void
    {
        $this->motorbikeId = $id;
        $this->weeklyRent = $rent !== null && $rent > 0 ? $rent : null;
        $this->step = 2;

        session([
            'flux_rental_intake_bike_id' => $id,
            'flux_rental_intake_weekly_rent' => $this->weeklyRent,
        ]);

        if ($this->draftBookingId && $this->customerId) {
            $this->syncDraft(2);
        }
    }

    public function selectCustomer(int $id): void
    {
        $this->customerId = $id;
        session()->forget(['flux_rental_intake_bike_id', 'flux_rental_intake_weekly_rent']);
        $this->step = 3;
        $this->syncDraft(3);
    }

    public function confirmTerms(): void
    {
        $this->validate([
            'startDate' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'termsAccepted' => ['accepted'],
        ], [
            'termsAccepted.accepted' => 'You must confirm the terms to continue.',
        ]);

        $this->step = 4;
        $this->syncDraft(4);
    }

    public function confirmPayment(): void
    {
        $dueToday = (float) ($this->weeklyRent ?? 0) + (float) $this->deposit;

        $this->validate([
            'weeklyRent' => ['required', 'numeric', 'min:0.01'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'initialPayment' => [
                'required',
                'numeric',
                'min:0',
                'max:'.number_format($dueToday, 2, '.', ''),
            ],
            'paymentMethod' => ['required', 'in:cash,card,bank,none'],
        ], [
            'weeklyRent.min' => 'Enter a weekly rent greater than zero.',
            'initialPayment.max' => 'Initial payment cannot exceed £'.number_format($dueToday, 2).' (weekly rent + deposit).',
        ]);

        if ((float) $this->initialPayment > 0 && $this->paymentMethod === 'none') {
            $this->addError('paymentMethod', 'Choose cash, card, or bank transfer when recording an amount received.');

            return;
        }

        if ((float) $this->initialPayment <= 0 && $this->paymentMethod !== 'none') {
            $this->paymentMethod = 'none';
        }

        try {
            $this->step = 5;
            $this->syncDraft(5);
        } catch (\Throwable $e) {
            $this->addError('initialPayment', $e->getMessage());
            $this->step = 4;
        }
    }

    public function createBooking(): void
    {
        $this->validate([
            'motorbikeId' => ['required', 'integer', Rule::exists('motorbikes', 'id')],
            'customerId' => ['required', 'integer', Rule::exists('customers', 'id')],
            'weeklyRent' => ['required', 'numeric', 'min:0.01'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'initialPayment' => ['nullable', 'numeric', 'min:0'],
            'startDate' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if (! $this->draftBookingId) {
            $this->syncDraft(5);
        } else {
            $this->syncDraft(5);
        }

        $bookingId = $this->draftBookingId;
        app(RentalIntakeDraft::class)->complete($bookingId, (int) auth()->id());

        $this->createdBookingId = $bookingId;
        if ($this->sendDocUploadLink && $this->customerId) {
            $this->buildCustomerUploadLink($bookingId);
        }

        session()->flash('status', 'Booking #'.$bookingId.' saved as same-day intake. Continue on the Documents tab.');

        $this->redirectRoute('flux-admin.rentals.show', [
            'booking' => $bookingId,
            'tab'     => 'documents',
        ], navigate: true);
    }

    private function buildCustomerUploadLink(int $bookingId): ?string
    {
        if (! $this->customerId) {
            return null;
        }

        try {
            return app(DocumentUploadAccessGenerator::class)->create(
                $this->customerId,
                $bookingId,
                sendEmail: false,
            )['uploadLink'];
        } catch (\Throwable) {
            return null;
        }
    }

    private function syncDraft(int $step): void
    {
        if (! $this->motorbikeId || ! $this->customerId) {
            return;
        }

        $this->draftBookingId = app(RentalIntakeDraft::class)->persist(
            $this->draftBookingId,
            (int) auth()->id(),
            $step,
            [
                'motorbike_id'         => $this->motorbikeId,
                'customer_id'          => $this->customerId,
                'start_date'           => $this->startDate,
                'weekly_rent'          => $this->weeklyRent,
                'deposit'              => $this->deposit,
                'notes'                => $this->notes,
                'terms_accepted'       => $this->termsAccepted,
                'payment_method'       => $this->paymentMethod,
                'initial_payment'      => $this->initialPayment,
                'send_doc_upload_link' => $this->sendDocUploadLink,
            ],
        );

        $this->createdBookingId = $this->draftBookingId;
    }

    private function resetWizard(): void
    {
        $this->reset([
            'step',
            'bikeSearch',
            'motorbikeId',
            'weeklyRent',
            'customerSearch',
            'customerId',
            'deposit',
            'initialPayment',
            'paymentMethod',
            'termsAccepted',
            'notes',
            'sendDocUploadLink',
            'docUploadLink',
            'draftBookingId',
            'createdBookingId',
            'resumableDraftId',
        ]);
        $this->step = 1;
        $this->startDate = AdminDateTimeInput::toLocal(now());
        $this->paymentMethod = 'cash';
        $this->sendDocUploadLink = true;
    }

    public function render()
    {
        $motorbikes = collect();
        if ($this->step === 1) {
            $query = Motorbike::query()
                ->leftJoin('motorbike_registrations as mr', function ($j) {
                    $j->on('mr.motorbike_id', '=', 'motorbikes.id')->where('mr.active', true);
                })
                ->leftJoin('renting_pricings as rp', function ($j) {
                    $j->on('rp.motorbike_id', '=', 'motorbikes.id')->where('rp.iscurrent', true);
                })
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('renting_booking_items as rbi')
                        ->join('renting_bookings as rb', 'rb.id', '=', 'rbi.booking_id')
                        ->whereRaw('rbi.motorbike_id = motorbikes.id')
                        ->where('rbi.is_posted', true)
                        ->where('rb.is_posted', true)
                        ->whereNull('rbi.end_date');
                })
                ->select([
                    'motorbikes.id',
                    'motorbikes.make',
                    'motorbikes.model',
                    'motorbikes.year',
                    'motorbikes.color',
                    'motorbikes.is_ebike',
                    'mr.registration_number as reg_no',
                    'rp.weekly_price as weekly_rent',
                    'rp.minimum_deposit as minimum_deposit',
                ]);

            if ($this->bikeSearch !== '') {
                $s = '%'.$this->bikeSearch.'%';
                $query->where(function ($q) use ($s) {
                    $q->where('motorbikes.make', 'like', $s)
                        ->orWhere('motorbikes.model', 'like', $s)
                        ->orWhere('mr.registration_number', 'like', $s);
                });
            }

            $motorbikes = $query->orderBy('motorbikes.make')->limit(50)->get();
        }

        $customers = collect();
        if ($this->step === 2) {
            $query = Customer::query()->select(['id', 'first_name', 'last_name', 'email', 'phone']);
            if ($this->customerSearch !== '') {
                $s = '%'.$this->customerSearch.'%';
                $query->where(function ($q) use ($s) {
                    $q->where('first_name', 'like', $s)
                        ->orWhere('last_name', 'like', $s)
                        ->orWhere('email', 'like', $s)
                        ->orWhere('phone', 'like', $s)
                        ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s);
                });
            }
            $customers = $query->orderBy('first_name')->limit(50)->get();
        }

        $selectedMotorbike = $this->motorbikeId
            ? Motorbike::query()
                ->leftJoin('motorbike_registrations as mr', function ($j) {
                    $j->on('mr.motorbike_id', '=', 'motorbikes.id')->where('mr.active', true);
                })
                ->where('motorbikes.id', $this->motorbikeId)
                ->select(['motorbikes.id', 'motorbikes.make', 'motorbikes.model', 'motorbikes.year', 'motorbikes.color', 'mr.registration_number as reg_no'])
                ->first()
            : null;

        $selectedCustomer = $this->customerId ? Customer::find($this->customerId) : null;

        return view('flux-admin.pages.rentals.new-booking-wizard', compact('motorbikes', 'customers', 'selectedMotorbike', 'selectedCustomer'));
    }
}
