<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DueInvoiceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailDataList;

    /**
     * Create a new message instance.
     */
    public function __construct($emailDataList)
    {
        $this->emailDataList = $emailDataList;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Daily Summary of Invoices Due Tomorrow')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.due_invoice_summary',
                ['emailDataList' => $this->emailDataList],
                'Daily Summary of Invoices Due Tomorrow',
            ));
    }
}
