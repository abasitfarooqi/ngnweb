<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Quick overwrite of club_members only from Cloudways production → local/DO target.
 */
class CloudwaysToDigitalOceanSyncClubMembersCommand extends Command
{
    protected $signature = 'cloudways-to-digital-ocean:sync-club-members';

    protected $description = 'Quick: overwrite only club_members from Cloudways production (latest full snapshot of that table).';

    public function handle(): int
    {
        return $this->call('cloudways-to-digital-ocean:sync-data', [
            '--only' => 'club_members',
        ]);
    }
}
