<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $senderName,
        public readonly string $senderEmail,
        public readonly string $phone,
        public readonly string $topic,
        public readonly string $messageBody,
        public readonly string $branchName = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[NGN Contact] '.$this->topic.' — '.$this->senderName,
            replyTo: [$this->senderEmail],
        );
    }

    public function content(): Content
    {
        $subject = '[NGN Contact] '.$this->topic.' — '.$this->senderName;

        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: [
                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    'emails.contact-submission',
                    [
                        'senderName' => $this->senderName,
                        'senderEmail' => $this->senderEmail,
                        'phone' => $this->phone,
                        'topic' => $this->topic,
                        'messageBody' => $this->messageBody,
                        'branchName' => $this->branchName,
                    ],
                    [
                        'title' => $subject,
                        'greetingName' => 'team',
                    ],
                ),
            ],
        );
    }
}
