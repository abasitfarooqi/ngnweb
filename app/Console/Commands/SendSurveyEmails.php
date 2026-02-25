<?php

namespace App\Console\Commands;

use App\Mail\NgnSurveySystemCampaignMailer;
use App\Models\NgnSurvey;
use App\Models\SurveyEmailCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSurveyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ngn-survey-system:execute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute NGN Survey Campaigns for sending emails';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $campaigns = SurveyEmailCampaign::where('is_sent', false)->get();
            $campaigns = $campaigns->filter(function ($campaign) {
                if ($campaign->is_sent) {
                    Log::info('Skipping already sent campaign for email: '.$campaign->email);

                    return false;
                }

                return true;
            });

            // Check for valid email and skip certain domains
            $campaigns = $campaigns->filter(function ($campaign) {
                $email = $campaign->email;
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $domain = substr(strrchr($email, '@'), 1);
                    if ($domain == 'email.ngn' || $domain == 'email.ngm' || $domain == 'ngm.com') {
                        Log::info('Skipping email with domain: '.$domain);

                        return false;
                    }

                    return true;
                } else {
                    Log::info('Invalid email: '.$email);

                    return false;
                }
            });

            foreach ($campaigns as $campaign) {
                if ($campaign->send_email) {
                    $survey = NgnSurvey::where('slug', 'scooter-preference-survey')->first();
                    $surveyLink = route('survey.showBySlug', ['slug' => $survey->slug]);
                    $emailData = [
                        'name' => $campaign->fullname,
                        'surveyLink' => $surveyLink,
                        'email' => $campaign->email,
                        'phone' => $campaign->phone,
                        'ngn_survey_id' => $campaign->ngn_survey_id,
                    ];

                    Mail::to($campaign->email)->send(new NgnSurveySystemCampaignMailer($emailData));

                    Log::info('Test email data: ', ['data' => $emailData]);

                    // Deactivate the campaign update for testing
                    $campaign->update([
                        'is_sent' => true,
                        'is_email_sent' => true,
                        'last_email_sent_datetime' => now(),
                    ]);

                } else {
                    Log::info('Skipping email as send_email is set to false for: '.$campaign->email);
                }
            }

            return 0;
        } catch (\Exception $e) {
            Log::error('Failed to send email to: '.$campaign->email.' with error: '.$e->getMessage());

            return 1;
        }
    }
}
