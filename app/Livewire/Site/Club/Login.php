<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $phone = '';

    public string $passkey = '';

    public function mount(): void
    {
        $q = request()->query('phone');
        if (is_string($q) && $q !== '') {
            $this->phone = $q;
        }
    }

    protected $rules = [
        'phone' => 'required|string|min:10|max:15',
        'passkey' => 'required|string|min:4|max:10',
    ];

    public function login(): void
    {
        $this->validate();

        // Normalise UK phone number
        $normalised = preg_replace('/\s+/', '', $this->phone);
        $normalised = preg_replace('/^\+44/', '0', $normalised);

        $member = ClubMember::where('phone', $normalised)
            ->where('passkey', $this->passkey)
            ->first();

        if (! $member) {
            $this->addError('passkey', 'Phone number or passkey does not match our records.');

            return;
        }

        if (! $member->is_active) {
            $this->addError('passkey', 'Your membership is not active. Please contact us.');

            return;
        }

        session(['club_member_id' => $member->id]);

        $this->redirectRoute('ngnclub.dashboard');
    }

    public function loginWithStaff(): void
    {
        // Redirect staff to admin area if already authenticated
        if (Auth::guard('web')->check()) {
            $this->redirect(url('/admin'), navigate: false);

            return;
        }
        $this->redirect(url('/admin/login'), navigate: false);
    }

    public function render()
    {
        return view('livewire.site.club.login')
            ->layout('components.layouts.public', [
                'title' => 'NGN Club Login | NGN Motors',
            ]);
    }
}
