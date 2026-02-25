<?php

namespace App\Console\Commands;

use App\Jobs\SendReferralCampaignEmailsJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReferralCampaignEmails extends Command
{
    protected $signature = 'send:referral-batch-emails';

    protected $description = 'Send referral campaign emails to all active club members';

    public function handle()
    {
        \Log::info('SendReferralCampaignBatchEmails command started at '.Carbon::now());
        dispatch(new SendReferralCampaignEmailsJob);
        $this->info('Referral campaign emails are being dispatched to the queue.');
        \Log::info('SendReferralCampaignBatchEmails command completed');
    }
}
