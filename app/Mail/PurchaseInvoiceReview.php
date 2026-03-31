<?php

namespace App\Mail;

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
            ->view('olders.emails.purchase-invoice-sign')
            ->with('mailData', $this->mailData);
    }
}
