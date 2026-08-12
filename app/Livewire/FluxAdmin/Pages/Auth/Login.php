<?php

namespace App\Livewire\FluxAdmin\Pages\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.guest')]
#[Title('Staff login')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user && (int) $user->is_admin === 1) {
            $this->redirect(route('flux-admin.dashboard'), navigate: true);
        }
    }

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $user = Auth::user();

        if (! $user || (int) $user->is_admin !== 1) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'You do not have access to Flux Admin.',
            ]);
        }

        session()->regenerate();

        $this->redirect(
            session()->pull('url.intended', route('flux-admin.dashboard')),
            navigate: true
        );
    }

    public function render()
    {
        return view('flux-admin.pages.auth.login');
    }
}
