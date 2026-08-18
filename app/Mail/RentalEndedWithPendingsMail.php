<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalEndedWithPendingsMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    /** @param  array<string, mixed>  $mailData */
    public function __construct(public array $mailData) {}

    public function envelope(): Envelope
    {
        $bookingId = $this->mailData['booking_id'] ?? '?';

        return new Envelope(
            subject: 'Rental ended with outstanding balances — booking #'.$bookingId,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.rental-ended-with-pendings',
            with: ['d' => $this->mailData],
        );
    }
}
