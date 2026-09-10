<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Models\CustomerAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class PortalEmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public readonly string $verificationUrl;

    public function __construct(public readonly CustomerAuth $customer)
    {
        $this->verificationUrl = URL::temporarySignedRoute(
            'customer.verification.verify',
            Carbon::now()->addMinutes((int) Config::get('auth.verification.expire', 60)),
            [
                'id' => $customer->getKey(),
                'hash' => sha1($customer->getEmailForVerification()),
            ]
        );
    }

    public function build(): static
    {
        return $this->subject('Verify your email address - NGN Motors')
            ->view('emails.portal.email-verification');
    }
}
