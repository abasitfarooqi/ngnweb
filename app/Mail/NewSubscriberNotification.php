<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
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
        return $this->subject('Welcome to NGN Club!')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ngnclub_new_subscriber_email',
                [
                    'clubMember' => $this->clubMember,
                    'passcode' => $this->passcode,
                ],
                'Welcome to NGN Club!',
            ));
    }
}
