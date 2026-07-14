<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SoldNewMotorbikesYearsReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $emailData
    ) {}

    public function build()
    {
        $range = $this->emailData['yearRangeLabel'] ?? '';

        return $this->subject('Sold motorbikes report ('.$range.')')
            ->view('emails.sold_new_motorbikes_years_report')
            ->with(['emailData' => $this->emailData]);
    }
}
