<?php

namespace App\Livewire\Site\Contact;

use App\Mail\ContactSubmission;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class TradeAccount extends Component
{
    public $companyName = '';

    public $contactName = '';

    public $email = '';

    public $phone = '';

    public $address = '';

    public $vatNumber = '';

    public $message = '';

    public function submitEnquiry(): void
    {
        $this->validate([
            'companyName' => 'required|string|min:2',
            'contactName' => 'required|string|min:2',
            'email' => 'required|email',
            'phone' => 'required|string|min:10',
            'address' => 'required|string|min:5',
            'message' => 'required|string|min:10',
            'vatNumber' => 'nullable|string|max:32',
        ]);

        $toEmail = config('mail.from.address', 'customerservice@neguinhomotors.co.uk');
        $body = 'Trade account application'."\n\n"
            .'Company: '.$this->companyName."\n"
            .'Business address:'."\n".$this->address."\n"
            .'VAT number: '.($this->vatNumber !== '' ? $this->vatNumber : '—')."\n\n"
            .'About the business:'."\n".$this->message;

        try {
            Mail::to($toEmail)->send(new ContactSubmission(
                senderName: $this->contactName,
                senderEmail: $this->email,
                phone: $this->phone,
                topic: 'Trade account',
                messageBody: $body,
                branchName: '',
            ));
        } catch (\Exception $e) {
            \Log::error('Trade account form mail failed: '.$e->getMessage());
        }

        session()->flash('success', 'Trade account enquiry received. Our team will contact you within 24 hours.');
        $this->reset(['companyName', 'contactName', 'email', 'phone', 'address', 'vatNumber', 'message']);
    }

    public function render()
    {
        return view('livewire.site.contact.trade-account')
            ->layout('components.layouts.public', [
                'title' => 'Trade Account Enquiry | NGN Motors',
            ]);
    }
}
