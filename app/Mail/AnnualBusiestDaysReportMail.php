<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnualBusiestDaysReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Annual Busiest Days Report')
            ->view('olders.emails.annual_busiest_days_report')
            ->with(['emailData' => $this->emailData]);
    }
}
