<?php

namespace App\Console\Commands;

use App\Mail\AnnualBusiestDaysReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAnnualBusiestDaysReport extends Command
{
    protected $signature = 'app:send-annual-busiest-days-report';
    protected $description = 'Sends annual report of busiest club member purchase days per branch and month totals';

    public function handle()
    {
        try {
            Log::info('Starting the annual busiest days report command.');

            $currentYear = date('Y');
            $yearStart = date('Y-m-d', strtotime('-1 year'));
            $yearEnd = date('Y-m-d');

            // Execute the combined query with proper MySQL date syntax
            $query = "
                select '--BRANCH--' as branch_id, '--DAY--' as period_type, '' as visit_count

                UNION ALL

                select cmp.branch_id, DAYNAME(cmp.date) as period_type, count(1) as visit_count
                from club_member_purchases cmp
                where cmp.date BETWEEN DATE_SUB(NOW(), INTERVAL 1 YEAR) AND NOW()
                AND cmp.branch_id IS NOT NULL
                group by cmp.branch_id, DAYNAME(cmp.date)

                UNION ALL

                select '--BRANCH--' as branch_id, '--MONTH--' as period_type, '' as visit_count

                UNION ALL

                select cmp.branch_id, MONTHNAME(cmp.date) as period_type, count(1) as visit_count
                from club_member_purchases cmp
                where cmp.date BETWEEN DATE_SUB(NOW(), INTERVAL 1 YEAR) AND NOW()
                AND cmp.branch_id IS NOT NULL
                group by cmp.branch_id, MONTHNAME(cmp.date)
            ";

            $results = DB::select($query);

            // Process results
            $dayData = [];
            $monthData = [];
            $dayReportByBranch = [];
            $monthReportByBranch = [];
            $isDaySection = false;
            $isMonthSection = false;

            foreach ($results as $row) {
                $branchId = $row->branch_id;
                $periodType = $row->period_type;
                $visitCount = $row->visit_count;

                // Check for separator rows
                if ($branchId === '--BRANCH--') {
                    if ($periodType === '--DAY--') {
                        $isDaySection = true;
                        $isMonthSection = false;
                    } elseif ($periodType === '--MONTH--') {
                        $isDaySection = false;
                        $isMonthSection = true;
                    }
                    continue;
                }

                // Process day data
                if ($isDaySection && !$isMonthSection) {
                    $dayName = $periodType;
                    if (!isset($dayData[$dayName])) {
                        $dayData[$dayName] = 0;
                    }
                    $dayData[$dayName] += (int)$visitCount;

                    // Organise by branch
                    if (!isset($dayReportByBranch[$branchId])) {
                        $dayReportByBranch[$branchId] = [
                            'branch_name' => $branchId,
                            'data' => []
                        ];
                    }
                    $dayReportByBranch[$branchId]['data'][] = (object)[
                        'day_name' => $dayName,
                        'total_visits' => (int)$visitCount
                    ];
                }

                // Process month data
                if ($isMonthSection && !$isDaySection) {
                    $monthName = $periodType;
                    if (!isset($monthData[$monthName])) {
                        $monthData[$monthName] = 0;
                    }
                    $monthData[$monthName] += (int)$visitCount;

                    // Organise by branch
                    if (!isset($monthReportByBranch[$branchId])) {
                        $monthReportByBranch[$branchId] = [
                            'branch_name' => $branchId,
                            'data' => []
                        ];
                    }
                    $monthReportByBranch[$branchId]['data'][] = (object)[
                        'month_name' => $monthName,
                        'total_visits' => (int)$visitCount
                    ];
                }
            }

            // Create ordered arrays for all days and months
            $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $allDaysReport = [];
            foreach ($dayOrder as $day) {
                $allDaysReport[] = (object)[
                    'day_name' => $day,
                    'total_visits' => $dayData[$day] ?? 0
                ];
            }

            $monthOrder = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $allMonthsReport = [];
            foreach ($monthOrder as $month) {
                $allMonthsReport[] = (object)[
                    'month_name' => $month,
                    'total_visits' => $monthData[$month] ?? 0
                ];
            }

            // Sort branch day data by day name
            foreach ($dayReportByBranch as $branchId => &$branchData) {
                usort($branchData['data'], function($a, $b) use ($dayOrder) {
                    $aIndex = array_search($a->day_name, $dayOrder);
                    $bIndex = array_search($b->day_name, $dayOrder);
                    return ($aIndex !== false ? $aIndex : 999) <=> ($bIndex !== false ? $bIndex : 999);
                });
            }

            // Sort branch month data by month name
            foreach ($monthReportByBranch as $branchId => &$branchData) {
                usort($branchData['data'], function($a, $b) use ($monthOrder) {
                    $aIndex = array_search($a->month_name, $monthOrder);
                    $bIndex = array_search($b->month_name, $monthOrder);
                    return ($aIndex !== false ? $aIndex : 999) <=> ($bIndex !== false ? $bIndex : 999);
                });
            }

            $emailData = [
                'allDaysReport' => $allDaysReport,
                'allMonthsReport' => $allMonthsReport,
                'dayReportByBranch' => $dayReportByBranch,
                'monthReportByBranch' => $monthReportByBranch,
                'year' => $currentYear,
                'yearStart' => $yearStart,
                'yearEnd' => $yearEnd
            ];

            // Send email
            Mail::to(['support@neguinhomotors.co.uk', 'thiago@neguinhomotors.co.uk'])
                ->send(new AnnualBusiestDaysReportMail($emailData));

            Log::info('Annual busiest days report emailed successfully.');
            $this->info('Annual busiest days report emailed successfully.');

        } catch (\Exception $e) {
            Log::error('Error sending annual busiest days report', ['error' => $e->getMessage()]);
            $this->error('An error occurred: '.$e->getMessage());
        }
    }
}
