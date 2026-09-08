<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalOtherChargePaymentReversedNotice extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(protected array $mailData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Important: Additional Charge Still Unpaid');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: [
                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.other-charge-payment-reversed',
                    $this->mailData,
                    ['title' => 'Additional Charge Still Unpaid'],
                ),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
