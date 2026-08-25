<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\RentingBooking;
use App\Models\RentingTransaction;
use App\Models\User;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentingDirectFreeWeekMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(
        public RentingBooking $booking,
        public BookingInvoice $invoice,
        public RentingTransaction $transaction,
        public Customer $hirer,
        public Customer $selectedCustomer,
        public string $proof,
        public ?int $handledByUserId = null,
        public float $amount = 0,
        public int $freeWeekOrdinal = 1,
        public int $freeWeekTotal = 1,
        public ?int $awardId = null,
        public ?int $consumedReferralId = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->freeWeekOrdinal > 1
                ? 'Staff direct free week '.$this->freeWeekOrdinal.' of '.$this->freeWeekTotal.' on booking #'.$this->booking->id
                : 'Staff direct free week on booking #'.$this->booking->id,
            cc: ['customerservice@neguinhomotors.co.uk'],
        );
    }

    public function content(): Content
    {
        $handler = $this->handledByUserId
            ? User::query()->find($this->handledByUserId)
            : null;

        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-direct-free-week',
                [
                    'booking' => $this->booking,
                    'invoice' => $this->invoice,
                    'transaction' => $this->transaction,
                    'hirer' => $this->hirer,
                    'selectedCustomer' => $this->selectedCustomer,
                    'proof' => $this->proof,
                    'handler' => $handler,
                    'amount' => $this->amount,
                    'freeWeekOrdinal' => $this->freeWeekOrdinal,
                    'freeWeekTotal' => $this->freeWeekTotal,
                    'awardId' => $this->awardId,
                    'consumedReferralId' => $this->consumedReferralId,
                ],
                'Staff direct free week',
            ),
        );
    }
}
