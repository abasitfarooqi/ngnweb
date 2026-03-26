<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
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

    public function joinClub(): void
    {
        $this->validate();

        $phone = preg_replace('/\s+/', '', $this->phone);
        $phone = preg_replace('/^\+44/', '0', $phone);

        $existing = ClubMember::where('email', $this->email)
            ->orWhere('phone', $phone)
            ->first();

        if ($existing) {
            $this->addError('email', 'A membership already exists with this email or phone number.');

            return;
        }

        ClubMember::create([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $phone,
            'make' => $this->make ?: null,
            'model' => $this->model ?: null,
            'year' => $this->year ?: null,
            'vrm' => strtoupper($this->vrm) ?: null,
            'tc_agreed' => true,
            'is_active' => true,
        ]);

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
