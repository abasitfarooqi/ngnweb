<?php

namespace App\Livewire\Site\Contact;

use Livewire\Component;

class TradeAccount extends Component
{
    public $company = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $vehicles = '';
    public $message = '';

    public function submit()
    {
        $this->validate([
            'company' => 'required|string|min:2',
            'name'    => 'required|string|min:2',
            'email'   => 'required|email',
            'phone'   => 'required|string|min:10',
        ]);
        session()->flash('success', 'Trade account enquiry received. Our team will contact you within 24 hours.');
        $this->reset(['company', 'name', 'email', 'phone', 'vehicles', 'message']);
    }

    public function render()
    {
        return view('livewire.site.contact.trade-account')
            ->layout('components.layouts.public', [
                'title' => 'Trade Account Enquiry | NGN Motors',
            ]);
    }
}
