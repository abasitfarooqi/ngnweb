<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MOT10DaysReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;

    public function __construct($subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function build()
    {
        return $this->subject('MOT Expiry Reminder - 10 Days')
            ->view('emails.mot-10days')
            ->with(['subscriber' => $this->subscriber]);
    }
}
