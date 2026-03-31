<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyBusiestDaysReportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailData;

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
        return $this->subject('Weekly Busiest Days Report Mail')
            ->view('olders.emails.weekly_busiest_days_report')
            ->with(['emailData' => $this->emailData]);
    }
}
