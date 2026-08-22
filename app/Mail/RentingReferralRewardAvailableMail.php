<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use App\Models\RentingReferral;
use App\Support\RentingReferralSettings;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentingReferralRewardAvailableMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    public function __construct(public RentingReferral $referral) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your rental referral reward is available');
    }

    public function content(): Content
    {
        $points = RentingReferralSettings::pointsPerQualifiedReferral();
        $rewardLine = $points === 100
            ? 'Your rental referral reward is now available ('.$points.' points — one free week).'
            : 'Your rental referral reward is now available ('.$points.' points).';

        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-referral-reward-available',
                [
                    'referral' => $this->referral,
                    'points' => $points,
                    'reward_line' => $rewardLine,
                ],
                'Your rental referral reward is available',
            ),
        );
    }
}
