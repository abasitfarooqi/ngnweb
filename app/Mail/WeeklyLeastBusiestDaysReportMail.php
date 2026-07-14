<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyLeastBusiestDaysReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->subject('Weekly Least Busiest Days Report')
            ->view('emails.weekly_least_busiest_days_report')
            ->with(['emailData' => $this->emailData]);
    }
}
