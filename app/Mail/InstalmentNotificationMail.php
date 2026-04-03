<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
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
        $title = 'Instalment Notification for '.$this->customer->regno;

        return $this->subject($title)
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.instalment_notification',
                [
                    'fullname' => $this->customer->fullname,
                    'regno' => $this->customer->regno,
                    'motorbike_id' => $this->customer->motorbike_id,
                ],
                $title,
            ));
    }
}
