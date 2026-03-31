<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSubscriberNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $clubMember;

    public $passcode;
    // public $terms;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($clubMember, $passcode)
    {
        $this->clubMember = $clubMember;
        $this->passcode = $passcode;
        // $this->terms = $terms;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('olders.emails.ngnclub_new_subscriber_email')
            ->subject('Welcome to NGN Club!')
            ->with([
                'clubMember' => $this->clubMember,
                'passcode' => $this->passcode,
                // 'terms' => $this->terms,
            ]);
    }
}
