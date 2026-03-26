<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $validated = $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $broker = config('fortify.passwords', 'users');
        $status = $this->attemptResetWithBroker($broker, $validated);

        if ($status !== Password::PASSWORD_RESET && $broker !== 'users') {
            $status = $this->attemptResetWithBroker('users', $validated);
        }

        if ($status === Password::PASSWORD_RESET) {
            $this->redirectRoute('login', navigate: true);
            session()->flash('status', __($status));
            return;
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }

    private function attemptResetWithBroker(string $broker, array $credentials): string
    {
        return Password::broker($broker)->reset(
            $credentials,
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );
    }
}
