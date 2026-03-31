<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteRequest extends Mailable
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
            subject: 'Quote Request',
        );
    }

    public function content()
    {
        return new Content(
            view: 'olders.emails.quote-request',
            with: $this->mailData
        );
    }

    public function attachments()
    {

        return [
            Attachment::fromData(fn () => $this->mailData['pdf']->output(), 'Quote Request.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
