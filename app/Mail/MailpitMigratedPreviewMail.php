<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sends one migrated email fragment through the universal shell (local Mailpit previews).
 */
final class MailpitMigratedPreviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly array $universalMailData,
        public readonly string $subjectLine,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: ['mailData' => $this->universalMailData],
        );
    }
}
