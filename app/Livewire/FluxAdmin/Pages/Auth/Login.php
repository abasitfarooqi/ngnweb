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

        if ($user && \App\Support\FluxAdminAccess::canEnterFluxAdmin($user)) {
            $this->redirect($this->homeRouteFor($user), navigate: true);
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

        if (! $user || ! \App\Support\FluxAdminAccess::canEnterFluxAdmin($user)) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'You do not have access to Flux Admin.',
            ]);
        }

        session()->regenerate();

        $this->redirect($this->homeRouteFor($user), navigate: true);
    }

    private function homeRouteFor($user): string
    {
        $intended = (string) session()->pull('url.intended', '');

        if (\App\Support\FluxAdminAccess::isCommunicationsOnlyStaff($user)) {
            if ($intended !== '' && str_contains($intended, '/flux-admin/communications')) {
                return $intended;
            }

            return route('flux-admin.communications.index');
        }

        return $intended !== '' ? $intended : route('flux-admin.dashboard');
    }

    public function render()
    {
        return view('flux-admin.pages.auth.login');
    }
}
