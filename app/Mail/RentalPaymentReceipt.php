<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalPaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    protected $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope()
    {
        $mailData = is_array($this->mailData) ? $this->mailData : (array) $this->mailData;

        return new Envelope(
            subject: (string) ($mailData['title'] ?? 'Rental Payment Receipt'),
        );
    }

    public function content()
    {
        $mailData = is_array($this->mailData) ? $this->mailData : (array) $this->mailData;

        return new Content(

            view: 'emails.templates.agreement-controller-universal',

            with: [

                'mailData' => UniversalMailPayload::fromLegacyEmailView(

                    'livewire.agreements.migrated.emails.rental-payment-receipt',

                    $mailData,

                    ['title' => $mailData['title'] ?? 'Hire Payment Receipt'],

                ),

            ],

        );
    }

    public function attachments()
    {
        return [];
    }
}
