<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NgnClubBatchUserCredentialsNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your NGN Club Login Credentials')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.club_batch_user_credentials',
                ['user' => $this->user],
                'Your NGN Club Login Credentials',
            ));
    }
}
