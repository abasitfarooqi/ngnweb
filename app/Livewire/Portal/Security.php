<?php

namespace App\Livewire\Portal;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Security extends Component
{
    public $current_password = '';

    public $password = '';

    public $password_confirmation = '';

    protected function canRegisteredCustomerChangePassword(): bool
    {
        $user = Auth::guard('customer')->user();
        if (! $user || ! $user->customer) {
            return false;
        }

        return (bool) $user->customer->is_register;
    }

    public function updatePassword()
    {
        if (! $this->canRegisteredCustomerChangePassword()) {
            $this->addError('password', 'Password changes are only available for registered club members.');

            return;
        }

        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::guard('customer')->user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');

            return;
        }

        $user->update(['password' => Hash::make($this->password)]);

        session()->flash('success', 'Password updated successfully.');
        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function resendVerificationEmail(): void
    {
        $user = Auth::guard('customer')->user();
        if (! $user || $user->hasVerifiedEmail()) {
            return;
        }

        $sent = RateLimiter::attempt(
            'email-verification:'.$user->id,
            6,
            function () use ($user) {
                $user->sendEmailVerificationNotification();
            },
            60
        );

        if (! $sent) {
            $this->addError('verification', 'Too many attempts. Please wait a minute and try again.');

            return;
        }

        $this->js("window.dispatchEvent(new CustomEvent('toast-show', { detail: { slots: { text: 'Please check your email and verify your address.' }, dataset: { variant: 'success' } } }))");
    }

    public function render()
    {
        $user = Auth::guard('customer')->user();
        $canChangePassword = $this->canRegisteredCustomerChangePassword();
        $twoFactorEnabled = false; // Fortify 2FA not enabled for customer guard
        $clubMember = $user?->customer?->clubMember;

        return view('livewire.portal.security', compact('user', 'twoFactorEnabled', 'canChangePassword', 'clubMember'))
            ->layout('components.layouts.portal', ['title' => 'Security Settings | My Account']);
    }
}
