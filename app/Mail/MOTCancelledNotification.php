<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MOTCancelledNotification extends Mailable
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
            subject: 'MOT Appointment Cancelled',
        );
    }

    public function content()
    {
        $data = is_array($this->mailData) ? $this->mailData : (array) $this->mailData;
        $title = trim((string) ($data['title'] ?? ''));
        $overrides = [
            'title' => $title !== '' ? $title : 'MOT Appointment Cancelled',
        ];

        return new Content(

            view: 'emails.templates.agreement-controller-universal',

            with: [

                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.mot_cancelled',
                    $data,
                    $overrides,
                ),

            ],

        );
    }
}
