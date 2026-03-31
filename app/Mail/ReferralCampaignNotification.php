<?php

namespace App\Mail;

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
        return $this->view('olders.emails.referral_campaign')
            ->subject('Hurry up! Earn £5 Credit')
            ->with(['user' => $this->user]);
    }
}
