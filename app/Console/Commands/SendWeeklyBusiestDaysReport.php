<?php

namespace App\Console\Commands;

use App\Mail\WeeklyBusiestDaysReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyBusiestDaysReport extends Command
{
    protected $signature = 'app:send-weekly-busiest-days-report';

    protected $description = 'Sends weekly report of busiest club member purchase days';

    public function handle()
    {
        try {
            Log::info('Starting the busiest days report command.');

            $topVisits = DB::select('
                WITH daily_visits AS (
                    SELECT 
                        DATE(date) AS visit_date,
                        DAYNAME(date) AS day_name,
                        YEARWEEK(date, 1) AS week_number,
                        COUNT(*) AS total_visits
                    FROM club_member_purchases
                    WHERE 
                        date >= CURDATE() - INTERVAL 30 DAY
                        AND branch_id IS NOT NULL
                    GROUP BY visit_date, day_name, week_number
                ),
                ranked_visits AS (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY week_number ORDER BY total_visits DESC) AS rank_in_week
                    FROM daily_visits
                )
                SELECT 
                    week_number,
                    visit_date,
                    day_name,
                    total_visits
                FROM ranked_visits
                WHERE rank_in_week = 1
                ORDER BY week_number
            ');

            $allVisits = DB::select('
                WITH daily_visits AS (
                    SELECT 
                        DATE(date) AS visit_date,
                        DAYNAME(date) AS day_name,
                        YEARWEEK(date, 1) AS week_number,
                        COUNT(*) AS total_visits
                    FROM club_member_purchases
                    WHERE 
                        date >= CURDATE() - INTERVAL 30 DAY
                        AND branch_id IS NOT NULL
                    GROUP BY visit_date, day_name, week_number
                ),
                ranked_visits AS (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY week_number ORDER BY total_visits DESC) AS rank_in_week
                    FROM daily_visits
                )
                SELECT * 
                FROM ranked_visits
                ORDER BY week_number, rank_in_week
            ');

            Log::info('Query executed successfully.', ['topVisits_count' => count($topVisits), 'allVisits_count' => count($allVisits)]);

            $emailData = [
                'topVisits' => $topVisits,
                'allVisits' => $allVisits,
            ];

            // Send email using the Mailable class
            Mail::to(['support@neguinhomotors.co.uk', 'thiago@neguinhomotors.co.uk'])
                ->send(new WeeklyBusiestDaysReportMail($emailData));
            Log::info('Email sent successfully.');
            $this->info('Weekly busiest days report emailed successfully.');
        } catch (\Exception $e) {
            Log::error('An error occurred while sending the busiest days report.', ['error' => $e->getMessage()]);
            $this->error('An error occurred: '.$e->getMessage());
        }
    }
}
