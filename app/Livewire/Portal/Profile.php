<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $phone = '';
    public $postcode = '';
    public $city = '';
    public $preferred_branch_id = '';

    public function mount()
    {
        $profile = Auth::guard('customer')->user()->profile;
        if ($profile) {
            $this->first_name          = $profile->first_name ?? '';
            $this->last_name           = $profile->last_name ?? '';
            $this->phone               = $profile->phone ?? '';
            $this->postcode            = $profile->postcode ?? '';
            $this->city                = $profile->city ?? '';
            $this->preferred_branch_id = $profile->preferred_branch_id ?? '';
        }
    }

    public function save()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'required|string|min:10',
        ]);

        $profile = Auth::guard('customer')->user()->profile;
        if ($profile) {
            $profile->update([
                'first_name'          => $this->first_name,
                'last_name'           => $this->last_name,
                'phone'               => $this->phone,
                'postcode'            => $this->postcode,
                'city'                => $this->city,
                'preferred_branch_id' => $this->preferred_branch_id ?: null,
            ]);
        }

        session()->flash('success', 'Profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.portal.profile')
            ->layout('components.layouts.portal');
    }
}
