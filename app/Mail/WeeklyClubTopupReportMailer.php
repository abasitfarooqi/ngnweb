<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyClubTopupReportMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Weekly Club Topup Report')
            ->view('emails.cron-jobs.cron-job-weekly-ngn-club-report')
            ->with('data', $this->data);
    }
}

