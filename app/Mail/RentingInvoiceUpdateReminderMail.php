<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\RentingBooking;
use App\Models\RentingWeeklyUpdate;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentingInvoiceUpdateReminderMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public const CC_EMAIL = 'customerservice@neguinhomotors.co.uk';

    public function __construct(
        public RentingWeeklyUpdate $update,
        public RentingBooking $booking,
        public BookingInvoice $invoice,
        public Customer $customer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder about your rental invoice #'.$this->invoice->id,
            cc: [self::CC_EMAIL],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-invoice-update-reminder',
                [
                    'update' => $this->update,
                    'booking' => $this->booking,
                    'invoice' => $this->invoice,
                    'customer' => $this->customer,
                    'customer_name' => trim($this->customer->first_name.' '.$this->customer->last_name),
                    'registration' => $this->registration(),
                    'weekly_rent' => $this->weeklyRent(),
                ],
                'Reminder about your rental invoice',
            ),
        );
    }

    private function registration(): string
    {
        $reg = $this->booking->rentingBookingItems
            ?->first(fn ($item) => filled($item->motorbike?->reg_no))
            ?->motorbike
            ?->reg_no;

        return $reg ? strtoupper((string) $reg) : '—';
    }

    private function weeklyRent(): ?float
    {
        $rent = $this->booking->rentingBookingItems?->first()?->weekly_rent;

        return $rent !== null ? (float) $rent : null;
    }
}
