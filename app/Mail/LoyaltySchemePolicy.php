<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoyaltySchemePolicy extends Mailable
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
            subject: 'Loyalty Upgrade Scheme Policy',
        );
    }

    public function content()
    {
        return new Content(
            view: 'olders.emails.loyalty-scheme-policy',
            with: $this->mailData
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (isset($this->mailData['pdf']) && method_exists($this->mailData['pdf'], 'output')) {
            $attachments[] = Attachment::fromData(
                fn () => $this->mailData['pdf']->output(),
                'Loyalty-Upgrade-Scheme-Policy.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
