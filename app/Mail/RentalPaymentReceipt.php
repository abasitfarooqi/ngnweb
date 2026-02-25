<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalPaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Hire Payment Receipt',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.rental-payment-receipt',
            with: $this->mailData
        );
    }

    public function attachments()
    {
        return [];
    }
}
