<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractsPendingLogbookReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $emailData
    ) {}

    public function build()
    {
        return $this->subject('Contracts awaiting logbook transfer (by contract month)')
            ->view('emails.contracts_pending_logbook_report')
            ->with(['emailData' => $this->emailData]);
    }
}
