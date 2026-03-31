<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyRentingReportMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $rows;

    /**
     * Create a new message instance.
     *
     * @param  \Illuminate\Support\Collection  $rows
     */
    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function build()
    {
        return $this->subject('Weekly Rental Profit Report')
            ->view('olders.emails.cron-jobs.weekly-renting-report')
            ->with('rows', $this->rows);
    }
}
