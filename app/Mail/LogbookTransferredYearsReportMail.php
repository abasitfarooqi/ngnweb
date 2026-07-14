<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LogbookTransferredYearsReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $emailData
    ) {}

    public function build()
    {
        return $this->subject('Logbook transferred report (2022–2025)')
            ->view('emails.logbook_transferred_years_report')
            ->with(['emailData' => $this->emailData]);
    }
}
