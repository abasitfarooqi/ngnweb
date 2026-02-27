<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $timezone = '';

    public function mount(): void
    {
        if (Auth::guard('customer')->check()) {
            $this->redirectRoute('account.dashboard');
        }
    }

    public function login(): void
    {
        $this->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::guard('customer')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        request()->session()->regenerate();

        if ($this->timezone !== '') {
            session(['timezone' => $this->timezone]);
        }

        $this->redirectIntended(route('account.dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login');
        // Layout from auth/login.blade.php <x-layouts.guest>
    }
}
