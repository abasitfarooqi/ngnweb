<?php

// File: app/Mail/Ecommerce/CustomerRegisterMailer.php

namespace App\Mail\Ecommerce;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerRegisterMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
        \Log::info('CustomerRegisterMailer data:', $this->mailData);
    }

    public function build()
    {
        $title = 'Welcome to NGN Store - Your One-Stop Shop for Motorcycles Rentals, Repairs, and Accessories!';

        return $this->subject($title)
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ecommerce.register',
                ['customer' => (object) $this->mailData['customer']],
                $title,
            ));
    }
}
