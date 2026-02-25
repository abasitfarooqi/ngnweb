<?php

// app/Jobs/SendBatchUserCredentials.php

namespace App\Jobs;

use App\Mail\NgnClubBatchUserCredentialsNotification;
use App\Models\ClubMember;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBatchUserCredentialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Skip if before Nov 30, 2024
        if (Carbon::now()->lt(Carbon::parse('2024-11-30'))) {
            return;
        }
        // Fetch only users who haven't been sent the email yet
        $users = ClubMember::where('email_sent', false)->get();

        foreach ($users as $user) {
            // Send email
            Mail::to($user->email)->send(new NgnClubBatchUserCredentialsNotification($user));

            // Mark the user as having received the email
            $user->email_sent = true;
            $user->save();
        }
    }
}
