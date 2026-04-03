<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralCampaignNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
        \Log::info('ReferralCampaignNotification constructor called for user: '.$user->id);
    }

    public function build()
    {
        return $this->subject('Hurry up! Earn £5 Credit')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.referral_campaign',
                ['user' => $this->user],
                'Hurry up! Earn £5 Credit',
            ));
    }
}
