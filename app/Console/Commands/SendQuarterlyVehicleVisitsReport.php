<?php

namespace App\Console\Commands;

use App\Mail\QuarterlyVehicleVisitsReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendQuarterlyVehicleVisitsReport extends Command
{
    protected $signature = 'app:send-quarterly-vehicle-visits-report';
    protected $description = 'Sends quarterly report of most and least visited vehicle makes and models';

    public function handle()
    {
        try {
            Log::info('Starting the quarterly vehicle visits report command.');

            // Get last 3 months period (quarterly report)
            $currentYear = Carbon::now()->year;
            $periodEnd = Carbon::now()->endOfDay();
            $periodStart = Carbon::now()->copy()->subMonths(3)->startOfDay();

            // Query for most visited makes and models - only showing visits >= 50
            $mostVisitedQuery = "
                WITH member_visits AS (
                    SELECT 
                        cm.make,
                        cm.model,
                        COUNT(*) AS total_visits
                    FROM club_member_purchases cmp
                    INNER JOIN club_members cm ON cmp.club_member_id = cm.id
                    WHERE cmp.created_at >= :periodStart
                      AND cmp.created_at <= :periodEnd
                      AND cm.make IS NOT NULL AND cm.model IS NOT NULL
                      AND TRIM(cm.make) != '' AND TRIM(cm.model) != ''
                    GROUP BY cm.make, cm.model
                    HAVING COUNT(*) >= 80
                ),
                make_totals AS (
                    SELECT 
                        make,
                        SUM(total_visits) AS make_total
                    FROM member_visits
                    GROUP BY make
                )
                SELECT 
                    CONCAT(mt.make, ' (', mt.make_total, ')') AS make,
                    mv.model,
                    mv.total_visits,
                    mt.make AS make_only
                FROM make_totals mt
                JOIN member_visits mv ON mv.make = mt.make
                ORDER BY mt.make_total DESC, mv.total_visits DESC
            ";

            $mostVisited = DB::select($mostVisitedQuery, [
                'periodStart' => $periodStart->toDateTimeString(),
                'periodEnd' => $periodEnd->toDateTimeString()
            ]);

            // Query for least visited makes and models - only showing visits <= 15
            $leastVisitedQuery = "
                WITH member_visits AS (
                    SELECT 
                        cm.make,
                        cm.model,
                        COUNT(*) AS total_visits
                    FROM club_member_purchases cmp
                    INNER JOIN club_members cm ON cmp.club_member_id = cm.id
                    WHERE cmp.created_at >= :periodStart
                      AND cmp.created_at <= :periodEnd
                      AND cm.make IS NOT NULL AND cm.model IS NOT NULL
                      AND TRIM(cm.make) != '' AND TRIM(cm.model) != ''
                    GROUP BY cm.make, cm.model
                    HAVING COUNT(*) <= 80 AND COUNT(*) > 5
                ),
                make_totals AS (
                    SELECT 
                        make,
                        SUM(total_visits) AS make_total
                    FROM member_visits
                    GROUP BY make
                )
                SELECT 
                    CONCAT(mt.make, ' (', mt.make_total, ')') AS make,
                    mv.model,
                    mv.total_visits,
                    mt.make AS make_only
                FROM make_totals mt
                JOIN member_visits mv ON mv.make = mt.make
                ORDER BY mt.make_total DESC, mv.total_visits DESC
            ";

            $leastVisited = DB::select($leastVisitedQuery, [
                'periodStart' => $periodStart->toDateTimeString(),
                'periodEnd' => $periodEnd->toDateTimeString()
            ]);

            // Query for most repeated model+year combinations
            $mostRepeatedModelYearQuery = "
                SELECT 
                    cm.make,
                    cm.model,
                    cm.year,
                    COUNT(*) AS repeat_count
                FROM club_member_purchases cmp
                INNER JOIN club_members cm ON cmp.club_member_id = cm.id
                WHERE cmp.created_at >= :periodStart
                  AND cmp.created_at <= :periodEnd
                  AND cm.make IS NOT NULL 
                  AND cm.model IS NOT NULL 
                  AND cm.year IS NOT NULL
                  AND TRIM(cm.make) != '' 
                  AND TRIM(cm.model) != ''
                  AND TRIM(cm.year) != ''
                GROUP BY cm.make, cm.model, cm.year
                HAVING COUNT(*) >= 10
                ORDER BY repeat_count DESC, cm.make ASC, cm.model ASC
                LIMIT 100
            ";

            $mostRepeatedModelYear = DB::select($mostRepeatedModelYearQuery, [
                'periodStart' => $periodStart->toDateTimeString(),
                'periodEnd' => $periodEnd->toDateTimeString()
            ]);

            // Query for least repeated model+year combinations
            $leastRepeatedModelYearQuery = "
                SELECT 
                    cm.make,
                    cm.model,
                    cm.year,
                    COUNT(*) AS repeat_count
                FROM club_member_purchases cmp
                INNER JOIN club_members cm ON cmp.club_member_id = cm.id
                WHERE cmp.created_at >= :periodStart
                  AND cmp.created_at <= :periodEnd
                  AND cm.make IS NOT NULL 
                  AND cm.model IS NOT NULL 
                  AND cm.year IS NOT NULL
                  AND TRIM(cm.make) != '' 
                  AND TRIM(cm.model) != ''
                  AND TRIM(cm.year) != ''
                GROUP BY cm.make, cm.model, cm.year
                HAVING COUNT(*) <= 5 AND COUNT(*) > 0
                ORDER BY repeat_count ASC, cm.make ASC, cm.model ASC
                LIMIT 100
            ";

            $leastRepeatedModelYear = DB::select($leastRepeatedModelYearQuery, [
                'periodStart' => $periodStart->toDateTimeString(),
                'periodEnd' => $periodEnd->toDateTimeString()
            ]);

            $emailData = [
                'mostVisited' => $mostVisited,
                'leastVisited' => $leastVisited,
                'mostRepeatedModelYear' => $mostRepeatedModelYear,
                'leastRepeatedModelYear' => $leastRepeatedModelYear,
                'year' => $currentYear,
                'periodStartFormatted' => $periodStart->format('d M Y'),
                'periodEndFormatted' => $periodEnd->format('d M Y'),
            ];

            // Send email
            Mail::to(['support@neguinhomotors.co.uk', 'thiago@neguinhomotors.co.uk'])
                ->send(new QuarterlyVehicleVisitsReportMail($emailData));

            Log::info('Quarterly vehicle visits report emailed successfully.');
            $this->info('Quarterly vehicle visits report emailed successfully.');

        } catch (\Exception $e) {
            Log::error('Error sending quarterly vehicle visits report', ['error' => $e->getMessage()]);
            $this->error('An error occurred: '.$e->getMessage());
        }
    }
}

