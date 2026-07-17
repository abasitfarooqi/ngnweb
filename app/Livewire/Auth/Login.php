<?php

namespace App\Livewire\Auth;

use App\Models\CustomerAuth;
use App\Support\CustomerPortalCredentialIssuer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

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

        $email = CustomerPortalCredentialIssuer::normaliseEmail($this->email);
        $user = CustomerAuth::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->first();

        if (! $user || ! Hash::check($this->password, (string) $user->password)) {
            $this->addError('email', 'These credentials do not match our records.');

            return;
        }

        Auth::guard('customer')->login($user, $this->remember);

        request()->session()->regenerate();

        $this->redirectIntended(route('account.dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login');
        // Layout from auth/login.blade.php <x-layouts.guest>
    }
}
