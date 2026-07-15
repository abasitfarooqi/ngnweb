<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
use App\Services\Club\ClubMemberSession;
use Livewire\Component;

class Index extends Component
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
        'year' => 'nullable|digits:4|max:4',
        'tc_agreed' => 'accepted',
    ];

    public function joinClub(): void
    {
        $this->validate();

        // Normalise phone
        $phone = preg_replace('/\s+/', '', $this->phone);
        $phone = preg_replace('/^\+44/', '0', $phone);

        // Check for existing membership
        $existing = ClubMember::where('email', $this->email)->orWhere('phone', $phone)->first();
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

        session()->flash('success', 'Welcome to NGN Club! We will be in touch with your passkey shortly.');
        $this->reset(['full_name', 'email', 'phone', 'make', 'model', 'year', 'vrm', 'tc_agreed']);
    }

    public function render()
    {
        $loggedInMember = ClubMemberSession::member();

        return view('livewire.site.club.index', compact('loggedInMember'))
            ->layout('components.layouts.public', [
                'title' => 'NGN Motorcycle Club | Exclusive Member Benefits | London',
                'description' => 'Join the NGN Motorcycle Club for exclusive discounts, events, and member-only benefits.',
            ]);
    }
}
