<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstalmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject('Instalment Notification for '.$this->customer->regno)
            ->view('emails.instalment_notification')
            ->with([
                'fullname' => $this->customer->fullname,
                'regno' => $this->customer->regno,
                'motorbike_id' => $this->customer->motorbike_id,
            ]);
    }
}
