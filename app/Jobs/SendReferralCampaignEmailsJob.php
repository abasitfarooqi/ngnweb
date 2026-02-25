<?php

// app/Jobs/SendReferralCampaignEmails.php

namespace App\Jobs;

use App\Mail\ReferralCampaignNotification;
use App\Models\ClubMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReferralCampaignEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        \Log::info('SendReferralCampaignEmails job started');
        $users = ClubMember::where('is_active', true)
            ->whereHas('purchases')
            ->get();

        \Log::info('Sending referral campaign emails to '.$users->count().' users');

        foreach ($users as $user) {
            Mail::to($user->email)->send(new ReferralCampaignNotification($user));
        }
    }
}
