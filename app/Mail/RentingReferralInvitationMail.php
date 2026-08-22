<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Models\RentingReferral;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentingReferralInvitationMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(public RentingReferral $referral) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'You have been referred to NGN Motors rentals');
    }

    public function content(): Content
    {
        $referrer = $this->referral->referrer;
        $referrerName = trim(($referrer->first_name ?? '').' '.($referrer->last_name ?? ''));

        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-referral-invitation',
                [
                    'referral' => $this->referral,
                    'referrer_name' => $referrerName !== '' ? $referrerName : 'A rental customer',
                    'share_url' => $this->referral->shareUrl(),
                ],
                'You have been referred to NGN Motors rentals',
            ),
        );
    }
}
