<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalOtherChargeReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param  array<string, mixed>  $mailData */
    public function __construct(public array $mailData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rental Other Charge Payment Reminder - Booking #'.($this->mailData['booking_id'] ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.rental-other-charge-reminder',
                ['charge' => $this->mailData],
                'Rental Other Charge Payment Reminder',
            ),
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
