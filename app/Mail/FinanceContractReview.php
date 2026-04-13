<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FinanceContractReview extends Mailable
{
    use Queueable, SerializesModels;

    protected $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contract Review',
        );
    }

    public function content()
    {
        return new Content(

            view: 'emails.templates.agreement-controller-universal',

            with: [

                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.rental-agreement-sign',
                    [
                        'mailData' => is_array($this->mailData) ? $this->mailData : (array) $this->mailData,
                    ],
                    ['title' => 'Contract Review'],
                ),

            ],

        );
    }

    public function attachments(): array
    {
        return [];
    }
}
