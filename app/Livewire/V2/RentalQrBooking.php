<?php

namespace App\Livewire\V2;

use App\Models\Motorbike;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Rule;
use Livewire\Component;

class RentalQrBooking extends Component
{
    public ?string $code = null;
    public ?Motorbike $bike = null;

    // Step 1: Bike details
    public int $step = 1;

    // Personal details
    #[Rule('required|string|max:100')]
    public string $full_name = '';

    #[Rule('required|email|max:150')]
    public string $email = '';

    #[Rule('required|string|max:20')]
    public string $phone = '';

    #[Rule('required|string|max:200')]
    public string $address = '';

    // Booking details
    #[Rule('required|date|after:today')]
    public string $start_date = '';

    #[Rule('required|in:1,3,7,14,30')]
    public string $duration = '1';

    #[Rule('nullable|string|max:500')]
    public string $notes = '';

    public bool $submitted = false;

    public function mount(?string $code = null): void
    {
        $this->code = $code;

        if ($code) {
            $this->bike = Motorbike::where('reg_no', strtoupper($code))
                ->orWhere('id', $code)
                ->first();
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validateOnly('full_name');
            $this->validateOnly('email');
            $this->validateOnly('phone');
            $this->validateOnly('address');
        }

        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function submit(): void
    {
        $this->validate();

        // TODO: store enquiry and send confirmation email
        $this->submitted = true;
    }

    public function render()
    {
        $availableBikes = Motorbike::latest()->take(20)->get();

        return view('livewire.v2.rental-qr-booking', compact('availableBikes'))
            ->layout('v2.layouts.app');
    }
}
