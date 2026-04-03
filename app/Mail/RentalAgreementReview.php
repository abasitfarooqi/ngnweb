<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Support\UniversalMailPayload;

class RentalAgreementReview extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rental Contract Review',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new Content(

            view: 'emails.templates.agreement-controller-universal',

            with: [

                'mailData' => UniversalMailPayload::fromLegacyEmailView(

                    'livewire.agreements.migrated.emails.rental-agreement-sign',

                    is_array($this->mailData) ? $this->mailData : (array) $this->mailData,

                    ['title' => 'Rental Contract Review'],

                ),

            ],

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
