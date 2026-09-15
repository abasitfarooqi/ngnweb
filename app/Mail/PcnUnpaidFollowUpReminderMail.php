<?php

namespace App\Mail;

use App\Models\PcnCase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PcnUnpaidFollowUpReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PcnCase $pcnCase) {}

    public function build(): static
    {
        return $this->subject('PCN customer payment follow-up: '.$this->pcnCase->pcn_number)
            ->view('emails.pcn-unpaid-follow-up-reminder');
    }
}
