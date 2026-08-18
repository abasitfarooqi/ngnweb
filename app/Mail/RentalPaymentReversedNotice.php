<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalPaymentReversedNotice extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    protected $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Important: Previous Receipt Ignored - Invoice Still Unpaid',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: [
                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.rental-payment-reversed-notice',
                    ['emailData' => is_array($this->mailData) ? $this->mailData : (array) $this->mailData],
                    ['title' => 'Payment Reversed']
                ),
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}
