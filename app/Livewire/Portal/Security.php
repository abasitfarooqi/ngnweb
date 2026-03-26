<?php

namespace App\Livewire\Portal;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Security extends Component
{
    public $current_password = '';

    public $password = '';

    public $password_confirmation = '';

    public function updatePassword()
    {
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

    public function render()
    {
        $user = Auth::guard('customer')->user();
        $twoFactorEnabled = false; // Fortify 2FA not enabled for customer guard

        return view('livewire.portal.security', compact('user', 'twoFactorEnabled'))
            ->layout('components.layouts.portal', ['title' => 'Security Settings | My Account']);
    }
}
