<?php

namespace Tests\Support;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class FakePolicyTestMail extends Mailable
{
    use UsesTransactionalCommunicationPolicy;

    public bool $includeTestAttachment = false;

    /** @var array<string, mixed> */
    public array $mailData = [];

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Policy test');
    }

    public function content(): Content
    {
        return new Content(htmlString: '<p>test</p>');
    }

    public function attachments(): array
    {
        if (! $this->includeTestAttachment) {
            return [];
        }

        return [
            Attachment::fromData(fn () => '%PDF-1.4 test', 'Policy-Test.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
