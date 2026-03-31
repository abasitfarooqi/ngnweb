<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NgnPartnerRegistrationMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $partnerData;

    /**
     * Create a new message instance.
     *
     * @param  array  $partnerData
     * @return void
     */
    public function __construct($partnerData)
    {
        $this->partnerData = $partnerData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Partner Registration')
            ->view('olders.emails.ngn-partner.partner_registration')
            ->with('partnerData', $this->partnerData);
    }
}
