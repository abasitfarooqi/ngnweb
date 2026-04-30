<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingPricing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

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

    public ?int $createdBookingId = null;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->startDate = now()->toDateString();
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
        $this->resetErrorBag();
        $this->step = $step;
    }

    public function selectMotorbike(int $id, ?float $rent = null): void
    {
        $this->motorbikeId = $id;
        $this->weeklyRent = $rent !== null && $rent > 0 ? $rent : ($this->weeklyRent ?? 0.0);
        $this->step = 2;
    }

    public function selectCustomer(int $id): void
    {
        $this->customerId = $id;
        $this->step = 3;
    }

    public function confirmTerms(): void
    {
        $this->validate([
            'startDate' => ['required', 'date'],
            'weeklyRent' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'termsAccepted' => ['accepted'],
        ], [
            'termsAccepted.accepted' => 'You must confirm the terms to continue.',
            'weeklyRent.min' => 'Enter a weekly rent greater than zero.',
        ]);
        $this->step = 4;
    }

    public function confirmPayment(): void
    {
        $this->validate([
            'deposit' => ['required', 'numeric', 'min:0'],
            'initialPayment' => ['required', 'numeric', 'min:0'],
            'paymentMethod' => ['required', 'in:cash,card,bank,none'],
        ]);
        $this->step = 5;
    }

    public function createBooking(): void
    {
        $this->validate([
            'motorbikeId' => ['required', 'integer', Rule::exists('motorbikes', 'id')],
            'customerId' => ['required', 'integer', Rule::exists('customers', 'id')],
            'weeklyRent' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'initialPayment' => ['nullable', 'numeric', 'min:0'],
            'startDate' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $bookingId = DB::transaction(function () {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $due = (clone $start)->addDays(7);

            $booking = RentingBooking::create([
                'customer_id' => $this->customerId,
                'user_id' => auth()->id(),
                'start_date' => $start->toDateString(),
                'due_date' => $due->toDateString(),
                'state' => 'DRAFT',
                'is_posted' => false,
                'deposit' => $this->deposit,
                'notes' => $this->notes !== '' ? $this->notes : null,
            ]);

            RentingBookingItem::create([
                'booking_id' => $booking->id,
                'motorbike_id' => $this->motorbikeId,
                'user_id' => auth()->id(),
                'weekly_rent' => $this->weeklyRent,
                'start_date' => $start->toDateString(),
                'due_date' => $due->toDateString(),
                'is_posted' => false,
            ]);

            if ($this->deposit > 0) {
                BookingInvoice::create([
                    'booking_id' => $booking->id,
                    'user_id' => auth()->id(),
                    'invoice_date' => $start->toDateString(),
                    'amount' => $this->deposit,
                    'deposit' => 1,
                    'state' => 'deposit',
                    'is_posted' => false,
                    'is_paid' => $this->initialPayment >= $this->deposit,
                    'paid_date' => $this->initialPayment >= $this->deposit ? now()->toDateString() : null,
                ]);
            }

            BookingInvoice::create([
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'invoice_date' => $start->toDateString(),
                'amount' => $this->weeklyRent,
                'deposit' => 0,
                'state' => 'weekly',
                'is_posted' => false,
                'is_paid' => false,
            ]);

            return $booking->id;
        });

        $this->createdBookingId = $bookingId;
        $this->docUploadLink = url('/generate-docs-upload-link-access/' . $this->customerId) . '?booking_id=' . $bookingId;
        $this->step = 6;
        session()->flash('status', 'Booking #' . $bookingId . ' created. Send the document upload link or open the booking to verify uploads.');
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
                $s = '%' . $this->bikeSearch . '%';
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
                $s = '%' . $this->customerSearch . '%';
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
