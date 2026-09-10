<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(
        public readonly string $email,
        public readonly string $resetUrl,
    ) {}

    public function build(): static
    {
        return $this->subject('Reset your NGN Motors portal password')
            ->view('emails.portal.password-reset');
    }
}
