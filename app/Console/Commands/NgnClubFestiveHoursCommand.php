<?php

namespace App\Console\Commands;

use App\Mail\NgnClubFestiveHoursMailer;
use App\Models\ClubMember;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NgnClubFestiveHoursCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ngn:club-festive-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-time job to send festive opening hours email to all NGN Club members';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Sending NGN Club festive hours email (EN + PT) to all members with an email address...');

        $count = 0;

        ClubMember::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', '!=', '-')
            ->chunkById(100, function ($members) use (&$count) {
                foreach ($members as $member) {
                    $email = trim((string) $member->email);

                    // Basic email validation to avoid runtime failures
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $this->warn("Skipping invalid email '{$email}' for ClubMember #{$member->id}");
                        continue;
                    }

                    try {
                        Mail::to($email)->queue(
                            new NgnClubFestiveHoursMailer($member)
                        );
                        $count++;
                    } catch (\Throwable $e) {
                        $this->error("Failed to queue email for ClubMember #{$member->id} ({$email}): {$e->getMessage()}");
                        // Continue with the next member
                    }
                }
            });

        $this->info("Queued festive hours emails for {$count} club members.");

        return self::SUCCESS;
    }
}


