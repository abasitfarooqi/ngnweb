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

class RentingReferralUnderReviewMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(public RentingReferral $referral) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'We have your rental referral');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-referral-under-review',
                [
                    'referral' => $this->referral,
                    'friend_name' => $this->referral->submitted_name,
                ],
                'We have your rental referral',
            ),
        );
    }
}
