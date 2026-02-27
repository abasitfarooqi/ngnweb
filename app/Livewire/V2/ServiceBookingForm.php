<?php

namespace App\Livewire\V2;

use Livewire\Attributes\Rule;
use Livewire\Component;

class ServiceBookingForm extends Component
{
    #[Rule('required|string|max:100')]
    public string $full_name = '';

    #[Rule('required|email|max:150')]
    public string $email = '';

    #[Rule('required|string|max:20')]
    public string $phone = '';

    #[Rule('required|string|max:20')]
    public string $reg_no = '';

    #[Rule('required|string|max:100')]
    public string $make_model = '';

    #[Rule('required|in:basic,full,repairs,other')]
    public string $service_type = 'basic';

    #[Rule('required|date|after:today')]
    public string $preferred_date = '';

    #[Rule('nullable|string|max:500')]
    public string $notes = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        // TODO: store and send confirmation email
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.v2.service-booking-form')
            ->layout('v2.layouts.app');
    }
}
