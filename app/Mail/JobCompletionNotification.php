<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobCompletionNotification extends Mailable
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
            subject: 'CRON JOB Notification',
        );
    }

    public function build()
    {
        return $this->subject('DVLA Check Job Completed')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.job-completion',
                ['mailData' => $this->mailData],
                'DVLA Check Job Completed',
            ));
    }

    public function attachments(): array
    {
        return [];
    }
}
