<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuarterlyVehicleVisitsReportMail extends Mailable
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
        return $this->subject('Quarterly Vehicle Visits Report')
            ->view('emails.quarterly_vehicle_visits_report')
            ->with(['emailData' => $this->emailData]);
    }
}

