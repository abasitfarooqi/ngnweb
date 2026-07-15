<?php

namespace App\Livewire\Site\Club;

use App\Services\Club\ClubMemberRegistrationService;
use Livewire\Component;

class Register extends Component
{
    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $make = '';

    public string $model = '';

    public string $year = '';

    public string $vrm = '';

    public bool $tc_agreed = false;

    protected $rules = [
        'full_name' => 'required|string|min:2|max:100',
        'email' => 'required|email|max:191',
        'phone' => 'required|string|min:10|max:15',
        'vrm' => 'nullable|string|max:10',
        'make' => 'nullable|string|max:50',
        'model' => 'nullable|string|max:50',
        'year' => 'nullable|digits:4',
        'tc_agreed' => 'accepted',
    ];

    public function joinClub(ClubMemberRegistrationService $registration): void
    {
        $this->validate();

        $result = $registration->register([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'vrm' => $this->vrm,
            'tc_agreed' => $this->tc_agreed,
        ]);

        if (! ($result['ok'] ?? false)) {
            foreach (($result['errors'] ?? []) as $field => $message) {
                $this->addError($field, $message);
            }

            return;
        }

        session()->flash('success', 'Welcome to NGN Club! Your passkey will be sent via SMS shortly. You can then login to your dashboard.');
        $this->reset(['full_name', 'email', 'phone', 'make', 'model', 'year', 'vrm', 'tc_agreed']);
    }

    public function render()
    {
        return view('livewire.site.club.register')
            ->layout('components.layouts.public', [
                'title' => 'Join NGN Club — Free Membership | NGN Motors London',
                'description' => 'Join NGN Club for free. Earn loyalty rewards, get MOT reminders and exclusive member discounts at all NGN branches.',
            ]);
    }
}
