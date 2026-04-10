<?php

namespace App\Livewire\Site\Finance;

use App\Http\Controllers\MailController;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use App\Models\ServiceBooking;
use Livewire\Component;

class Index extends Component
{
    public $loanAmount = 3000;

    public $deposit = 500;

    public $term = 12;

    /** @var float Calculator interest rate (% APR) — fixed at 0; field hidden on public finance page */
    public $rate = 0;

    public $monthlyPayment = 0;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public ?string $employmentStatus = null;

    public ?string $bikeInterest = null;

    public ?string $notes = null;

    public bool $consent = false;

    // Optional context when coming from a bike card (used/new)
    public ?int $bikeId = null;

    public ?string $bikeType = null;

    public ?string $bikeSource = null;

    public ?float $bikePrice = null;

    protected $rules = [
        'firstName' => 'required|string|min:2',
        'lastName' => 'required|string|min:2',
        'email' => 'required|email',
        'phone' => 'required|string|min:10',
        'employmentStatus' => 'required|string',
        'bikeInterest' => 'nullable|string',
        'notes' => 'nullable|string',
        'consent' => 'accepted',
    ];

    public function mount(): void
    {
        // Populate from query string when coming from /bikes pages
        $this->bikeId = request()->integer('bike_id') ?: null;
        $this->bikeType = request()->string('bike_type')->lower()->value() ?: null;
        $this->bikeSource = request()->string('source')->value() ?: null;
        $this->bikePrice = request()->has('price') ? (float) request('price') : null;

        if ($this->bikePrice !== null) {
            $this->loanAmount = max(0, (float) $this->bikePrice);
        }

        if ($this->bikeId && $this->bikeType) {
            $this->prefillBikeInterest();
        }

        $this->calculatePayment();
    }

    public function updated($field): void
    {
        if ($field === 'term') {
            $t = (int) $this->term;
            $this->term = in_array($t, [6, 12], true) ? $t : 12;
        }
        if (in_array($field, ['loanAmount', 'deposit', 'term', 'rate'], true)) {
            $this->calculatePayment();
        }
    }

    public function calculatePayment(): void
    {
        $principal = max(0, $this->loanAmount - $this->deposit);
        $apr = max(0, (float) $this->rate);
        $monthlyRate = ($apr / 100) / 12;

        if ($this->term < 1) {
            $this->monthlyPayment = 0;

            return;
        }

        if ($monthlyRate > 0) {
            $this->monthlyPayment = round(
                $principal * ($monthlyRate * pow(1 + $monthlyRate, $this->term)) /
                (pow(1 + $monthlyRate, $this->term) - 1),
                2
            );
        } else {
            $this->monthlyPayment = round($principal / $this->term, 2);
        }
    }

    protected function prefillBikeInterest(): void
    {
        $summary = null;

        if ($this->bikeType === 'used') {
            $bike = Motorbike::find($this->bikeId);

            if ($bike) {
                $maskedReg = $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : null;
                $parts = array_filter([
                    trim(($bike->make ?? '').' '.($bike->model ?? '')),
                    $maskedReg,
                    $bike->year,
                ]);

                if ($parts !== []) {
                    $summary = implode(' · ', $parts);
                }
            }
        } elseif ($this->bikeType === 'new') {
            $bike = Motorcycle::find($this->bikeId);

            if ($bike) {
                $parts = array_filter([
                    trim(($bike->make ?? '').' '.($bike->model ?? '')),
                    $bike->year,
                ]);

                if ($parts !== []) {
                    $summary = implode(' · ', $parts);
                }
            }
        }

        if ($summary) {
            $this->bikeInterest = $summary;
        }
    }

    public function submitApplication(): void
    {
        $this->validate();

        $fullName = trim($this->firstName.' '.$this->lastName);
        $amount = (float) $this->loanAmount;
        $deposit = (float) $this->deposit;
        $bikeLabel = trim((string) ($this->bikeInterest ?? ''));
        $bikeType = (string) ($this->bikeType ?? 'unknown');
        $source = (string) ($this->bikeSource ?? 'finance-page');

        $description = implode(' | ', array_filter([
            'Source: '.$source,
            $bikeLabel !== '' ? 'Bike: '.$bikeLabel : null,
            $this->bikeId ? 'Bike ID: '.$this->bikeId : null,
            'Bike type: '.$bikeType,
            'Finance amount GBP: '.number_format($amount, 2, '.', ''),
            'Deposit GBP: '.number_format($deposit, 2, '.', ''),
            'Term months: '.(int) $this->term,
            'Illustrative monthly from calculator: (bike price − deposit) ÷ term months; no interest on this calculator — not an offer of credit',
            'Employment: '.(string) $this->employmentStatus,
            $this->notes ? 'Notes: '.trim((string) $this->notes) : null,
        ]));

        $customerAuth = auth('customer')->user();

        $booking = ServiceBooking::query()->create([
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
            'enquiry_type' => 'finance',
            'service_type' => 'Finance enquiry',
            'subject' => $bikeLabel !== '' ? 'Finance enquiry - '.$bikeLabel : 'Finance enquiry',
            'description' => $description,
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => $fullName,
            'phone' => trim($this->phone),
            'reg_no' => $this->bikeId ? 'Finance bike #'.$this->bikeId : 'Finance enquiry',
            'email' => trim($this->email),
        ]);

        app(MailController::class)->sendBookingConfirmation($booking);

        session()->flash('success', 'Finance enquiry received. We will contact you within 24 hours.');

        $this->resetValidation();
        $this->reset([
            'firstName',
            'lastName',
            'email',
            'phone',
            'employmentStatus',
            'bikeInterest',
            'notes',
            'consent',
        ]);

        // Keep calculator + bike context so the page still reflects the bike
        $this->calculatePayment();
    }

    public function render()
    {
        return view('livewire.site.finance.index')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Finance London | Flexible Payment Plans | NGN Motors',
                'description' => 'Flexible motorcycle finance options in London. Apply online today.',
            ]);
    }
}
