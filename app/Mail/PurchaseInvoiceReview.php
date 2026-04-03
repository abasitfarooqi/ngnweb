<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseInvoiceReview extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Purchase Invoice Review',
    //     );
    // }

    public function build()
    {
        return $this->subject('Purchase Invoice Review')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.purchase-invoice-sign',
                ['mailData' => $this->mailData],
                'Purchase Invoice Review',
            ));
    }
}
