<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LogBookTransferMail extends Mailable
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
            subject: 'Log Book Transfer Mail',
        );
    }

    public function content()
    {
        return new Content(
            view: 'olders.emails.logbook-transfer',
            with: $this->mailData
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
