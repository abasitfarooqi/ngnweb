<?php

namespace App\Console\Commands;

use App\Models\NgnSurvey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateSurveyCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-survey-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate Survey Campaigns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Starting the survey campaign population process...');

        $clubMembers = DB::table('club_members')
            ->select('full_name as name', 'email', 'phone')
            ->get();

        $customers = DB::table('customers')
            ->select(DB::raw('CONCAT(first_name, " ", last_name) as name'), 'email', 'phone')
            ->get();

        $combined = $clubMembers->merge($customers);

        $uniqueEmails = $combined->unique('email');

        $uniqueEmails = $uniqueEmails->filter(function ($record) {
            return filter_var($record->email, FILTER_VALIDATE_EMAIL);
        });

        // $uniqueEmails = $uniqueEmails->reject(function ($record) {
        //     $isRejected = strpos($record->email, '@email.ngn') !== false || strpos($record->email, '@email.ngm') !== false || strpos($record->email, '@ngm.com') !== false;
        //     if ($isRejected) {
        //         Log::info('Survey Campaign: Skipped record for email: ' . $record->email . ' - ' . $record->name . ' - ' . $record->phone);
        //     }
        //     return $isRejected;
        // });

        $survey = NgnSurvey::where('slug', 'scooter-preference-survey')->first();

        foreach ($uniqueEmails as $record) {
            DB::table('survey_email_campaigns')->updateOrInsert(
                ['email' => $record->email],
                [
                    'ngn_survey_id' => $survey->id,
                    'fullname' => $record->name,
                    'phone' => $record->phone,
                    'send_email' => true,
                    'send_phone' => false,
                    'is_sent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            Log::info('Survey Campaign: Inserted/Updated record for email: '.$record->email.' - '.$record->name.' - '.$record->phone.' - '.$survey->id.' - '.$survey->title.' - '.$survey->slug);
        }

        Log::info('Survey Campaign: Population process completed successfully.');
    }
}
