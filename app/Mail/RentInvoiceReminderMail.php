<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RentInvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject('Weekly Rent Notification for '.$this->customer->reg_no)
            ->view('olders.emails.rent_notification')
            ->with(['customer' => $this->customer]);
    }
}
