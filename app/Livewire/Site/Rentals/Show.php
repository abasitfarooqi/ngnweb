<?php

namespace App\Livewire\Site\Rentals;

use App\Http\Controllers\MailController;
use App\Models\Motorbike;
use App\Models\ServiceBooking;
use Livewire\Component;

class Show extends Component
{
    public $motorbike;

    public $pricing;

    public $selectedPeriod = 'weekly';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public bool $privacy = false;

    public function mount($id)
    {
        try {
            $this->motorbike = Motorbike::with(['images', 'currentRentingPricing', 'branch'])
                ->findOrFail($id);
            $this->pricing = $this->motorbike->currentRentingPricing;
        } catch (\Exception $e) {
            abort(404, 'Motorcycle not found');
        }

        $customer = auth('customer')->user();
        if ($customer?->email) {
            $this->email = (string) $customer->email;
        }
        if ($customer?->customer?->phone) {
            $this->phone = (string) $customer->customer->phone;
        }
        if ($customer?->customer?->full_name) {
            $this->name = (string) $customer->customer->full_name;
        }
        $this->message = sprintf(
            'I want to rent %s %s. Please share next steps.',
            (string) ($this->motorbike->make ?? ''),
            (string) ($this->motorbike->model ?? '')
        );
    }

    public function setPeriod($period)
    {
        $this->selectedPeriod = $period;
    }

    public function calculatePrice()
    {
        if (! $this->pricing) {
            return 80;
        }

        return match ($this->selectedPeriod) {
            'daily' => round($this->pricing->weekly_price / 6, 2),
            'weekly' => $this->pricing->weekly_price,
            'monthly' => round($this->pricing->weekly_price * 4 * 0.9, 2),
            default => $this->pricing->weekly_price,
        };
    }

    public function submitEnquiry(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'min:7'],
            'message' => ['required', 'string', 'min:5'],
            'privacy' => ['accepted'],
        ]);

        $booking = ServiceBooking::query()->create([
            'service_type' => 'Motorcycle rental enquiry',
            'description' => implode(' | ', array_filter([
                'Source: livewire.site.rentals.show',
                'Bike: '.trim((string) ($this->motorbike->make ?? '').' '.(string) ($this->motorbike->model ?? '')),
                'Bike ID: '.(string) ($this->motorbike->id ?? ''),
                $this->motorbike->reg_no ? 'Reg: '.$this->motorbike->reg_no : null,
                'Period: '.$this->selectedPeriod,
                'Quoted GBP: '.number_format((float) $this->calculatePrice(), 2, '.', ''),
                'Customer message: '.trim($this->message),
            ])),
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => trim($this->name),
            'phone' => trim($this->phone),
            'reg_no' => (string) ($this->motorbike->reg_no ?? 'N/A'),
            'email' => trim($this->email) ?: null,
        ]);

        app(MailController::class)->sendBookingConfirmation($booking);
        session()->flash('enquiry_success', 'Rental enquiry sent. Our team will contact you shortly.');
        $this->privacy = false;
    }

    public function render()
    {
        $currentPrice = $this->calculatePrice();

        return view('livewire.site.rentals.show', compact('currentPrice'))
            ->layout('components.layouts.public', [
                'title' => $this->motorbike->make.' '.$this->motorbike->model.' - Motorcycle Rental | NGN Motors',
                'description' => 'Rent '.$this->motorbike->make.' '.$this->motorbike->model.' from £'.$currentPrice.'/'.$this->selectedPeriod.'.',
            ]);
    }
}
