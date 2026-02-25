<?php

namespace App\Console\Commands;

use App\Models\ClubMember;
use App\Models\UserSegment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SegmentUsers extends Command
{
    protected $signature = 'users:segment';

    protected $description = 'Segment users based on their interaction patterns';

    public function handle()
    {
        $clubMembers = ClubMember::all();

        foreach ($clubMembers as $member) {
            $sessions = $member->sessions()->where('login_time', '>=', Carbon::now()->subDays(30))->get();

            $loginCount = $sessions->count();
            $averageDuration = $sessions->avg('session_duration');

            if ($loginCount > 10) {
                $segmentType = 'Frequent User';
            } elseif ($averageDuration > 300) {
                $segmentType = 'Engaged User';
            } else {
                $segmentType = 'Casual User';
            }

            UserSegment::updateOrCreate(
                ['club_member_id' => $member->id],
                ['segment_type' => $segmentType]
            );
        }

        $this->info('User segmentation completed.');
    }
}
