<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteRequest extends Mailable
{
    use Queueable, SerializesModels;

    protected $mailData;

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

            view: 'emails.templates.agreement-controller-universal',

            with: [

                'mailData' => UniversalMailPayload::fromLegacyEmailView(

                    'livewire.agreements.migrated.emails.quote-request',

                    is_array($this->mailData) ? $this->mailData : (array) $this->mailData,

                    ['title' => 'Quote Request'],

                ),

            ],

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
