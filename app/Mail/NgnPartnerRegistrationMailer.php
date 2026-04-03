<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
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
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ngn-partner.partner_registration',
                ['partnerData' => $this->partnerData],
                'New Partner Registration',
            ));
    }
}
