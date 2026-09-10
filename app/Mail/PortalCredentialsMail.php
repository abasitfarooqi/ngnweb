<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalCredentialsMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(
        public readonly string $email,
        public readonly string $temporaryPassword,
        public readonly string $portalUrl,
    ) {}

    public function build(): static
    {
        return $this->subject('Your NGN Motors portal credentials')
            ->view('emails.portal.credentials');
    }
}
