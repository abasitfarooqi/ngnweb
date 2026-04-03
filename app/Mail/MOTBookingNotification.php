<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Support\UniversalMailPayload;

class MOTBookingNotification extends Mailable
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
            subject: 'MOT Appointment Confirmation',
        );
    }

    public function content()
    {
        return new Content(

            view: 'emails.templates.agreement-controller-universal',

            with: [

                'mailData' => UniversalMailPayload::fromLegacyEmailView(

                    'livewire.agreements.migrated.emails.mot_booking_confirmation',

                    is_array($this->mailData) ? $this->mailData : (array) $this->mailData,

                    ['title' => 'MOT Appointment Confirmation'],

                ),

            ],

        );
    }

    public function attachments(): array
    {
        return [];
    }

}
