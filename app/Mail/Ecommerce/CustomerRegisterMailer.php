<?php

// File: app/Mail/Ecommerce/CustomerRegisterMailer.php

namespace App\Mail\Ecommerce;

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
        return $this->view('olders.emails.ecommerce.register')
            ->subject('Welcome to NGN Store - Your One-Stop Shop for Motorcycles Rentals, Repairs, and Accessories!')
            ->with([
                'customer' => (object) $this->mailData['customer'],
            ]);
    }
}
