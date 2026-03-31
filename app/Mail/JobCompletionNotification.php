<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobCompletionNotification extends Mailable
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
            subject: 'CRON JOB Notification',
        );
    }

    public function build()
    {
        return $this->subject('DVLA Check Job Completed')
            ->view('olders.emails.job-completion')
            ->with('mailData', $this->mailData);
    }

    public function attachments(): array
    {
        return [];
    }
}
