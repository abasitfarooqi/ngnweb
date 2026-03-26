<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
        ]);

        $primaryBroker = config('fortify.passwords', 'users');
        $status = Password::broker($primaryBroker)->sendResetLink(['email' => $validated['email']]);

        if ($status === Password::INVALID_USER && $primaryBroker !== 'users') {
            $status = Password::broker('users')->sendResetLink(['email' => $validated['email']]);
        }

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));
            return;
        }

        if ($status === Password::INVALID_USER) {
            $this->addError('email', "We can't find a user with that email address.");
            return;
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
