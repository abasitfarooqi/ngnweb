<?php

namespace App\Livewire\Site\Club;

use Livewire\Component;

class Index extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $bikeDetails = '';
    public $agreedTerms = false;

    protected $rules = [
        'name'         => 'required|string|min:2',
        'email'        => 'required|email',
        'phone'        => 'required|string|min:10',
        'bikeDetails'  => 'nullable|string',
        'agreedTerms'  => 'accepted',
    ];

    public function joinClub()
    {
        $this->validate();
        session()->flash('success', 'Welcome to NGN Club! Check your email for your membership details.');
        $this->reset(['name', 'email', 'phone', 'bikeDetails', 'agreedTerms']);
    }

    public function render()
    {
        return view('livewire.site.club.index')
            ->layout('components.layouts.public', [
                'title' => 'NGN Motorcycle Club | Exclusive Member Benefits | London',
                'description' => 'Join the NGN Motorcycle Club for exclusive discounts, events, and member-only benefits.',
            ]);
    }
}
