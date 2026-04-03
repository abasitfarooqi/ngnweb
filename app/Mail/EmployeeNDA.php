<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeNDA extends Mailable
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
            subject: 'Employee NDA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: ['mailData' => $this->mailData],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->mailData['pdf']->output(), 'Signed-Employee-NDA.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
