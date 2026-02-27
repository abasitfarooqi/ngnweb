<?php

namespace App\Livewire\Site\Mot;

use Livewire\Component;

class Alert extends Component
{
    public $firstName = '';
    public $lastName = '';
    public $email = '';
    public $phone = '';
    public $regNo = '';
    public $notifyEmail = true;
    public $notifyPhone = false;
    public $enableDeals = false;

    protected $rules = [
        'firstName' => 'required|string|min:2',
        'lastName'  => 'required|string|min:2',
        'email'     => 'required|email',
        'phone'     => 'required|string|min:10',
        'regNo'     => 'required|string|min:2|max:10',
    ];

    public function submitAlert()
    {
        $this->validate();
        $this->regNo = strtoupper(str_replace(' ', '', $this->regNo));
        session()->flash('success', 'MOT/Tax alert registered successfully!');
        $this->reset(['firstName', 'lastName', 'email', 'phone', 'regNo']);
    }

    public function render()
    {
        return view('livewire.site.mot.alert');
    }
}
