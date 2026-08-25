<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use App\Models\RentingReferral;
use App\Models\RentingTransaction;
use App\Models\User;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentingReferralStaffInvoiceMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(
        public RentingReferral $referral,
        public string $event,
        public ?int $handledByUserId = null,
        public ?BookingInvoice $invoice = null,
        public ?RentingBooking $booking = null,
        public ?RentingTransaction $transaction = null,
        public ?float $amount = null,
        public ?string $proof = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->event) {
            'redeemed' => 'Rental referral free week applied #'.$this->referral->id,
            'ready' => 'Rental referral points ready to use #'.$this->referral->id,
            default => 'Rental referral staff notice #'.$this->referral->id,
        };

        return new Envelope(
            subject: $subject,
            cc: ['customerservice@neguinhomotors.co.uk'],
        );
    }

    public function content(): Content
    {
        $handler = $this->handledByUserId
            ? User::query()->find($this->handledByUserId)
            : null;

        $title = match ($this->event) {
            'redeemed' => 'Rental referral free week applied',
            'ready' => 'Rental referral points ready to use',
            default => 'Rental referral staff notice',
        };

        $intro = match ($this->event) {
            'redeemed' => 'A rental referral free week has been applied. No money was taken. A real rental referral reward transaction marked the referrer’s invoice paid.',
            'ready' => 'This referrer’s points are now ready to use. Staff can apply one free week to an unpaid invoice. This is a one-time reward.',
            default => 'Rental referral staff notice.',
        };

        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-referral-staff-invoice',
                [
                    'referral' => $this->referral->loadMissing(['referrer', 'referred', 'referrerQualifyingBooking', 'referredQualifyingBooking', 'referredQualifyingInvoice']),
                    'event' => $this->event,
                    'intro' => $intro,
                    'handler' => $handler,
                    'invoice' => $this->invoice,
                    'booking' => $this->booking,
                    'transaction' => $this->transaction,
                    'amount' => $this->amount,
                    'proof' => $this->proof,
                ],
                $title,
            ),
        );
    }
}
