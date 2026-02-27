<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Security extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::guard('customer')->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->password)]);

        session()->flash('success', 'Password updated successfully.');
        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function render()
    {
        return view('livewire.portal.security')
            ->layout('components.layouts.portal');
    }
}
