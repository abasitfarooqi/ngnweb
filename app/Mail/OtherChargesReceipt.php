<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtherChargesReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Other Charges Receipt',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.othercharges-receipt',
            with: $this->mailData
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
