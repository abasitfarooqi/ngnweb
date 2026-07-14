<?php

namespace App\Console\Commands;

use App\Mail\WeeklyLeastBusiestDaysReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyLeastBusiestDaysReport extends Command
{
    protected $signature = 'app:send-weekly-least-busiest-days-report';
    protected $description = 'Sends weekly report of least busiest club member purchase days (bottom to top)';

    public function handle()
    {
        try {
            Log::info('Starting the weekly least busiest days report command.');

            $weekEnd = date('Y-m-d');
            $weekStart = date('Y-m-d', strtotime('-7 days'));

            $dayResults = DB::select("
                SELECT DAYNAME(cmp.date) AS day_name, COUNT(1) AS total_visits
                FROM club_member_purchases cmp
                WHERE cmp.date BETWEEN ? AND ?
                AND cmp.branch_id IS NOT NULL
                GROUP BY DAYNAME(cmp.date)
            ", [$weekStart, $weekEnd]);

            $branchDayResults = DB::select("
                SELECT cmp.branch_id, DAYNAME(cmp.date) AS day_name, COUNT(1) AS total_visits
                FROM club_member_purchases cmp
                WHERE cmp.date BETWEEN ? AND ?
                AND cmp.branch_id IS NOT NULL
                GROUP BY cmp.branch_id, DAYNAME(cmp.date)
            ", [$weekStart, $weekEnd]);

            $branchTotals = DB::select("
                SELECT cmp.branch_id, COUNT(1) AS total_visits
                FROM club_member_purchases cmp
                WHERE cmp.date BETWEEN ? AND ?
                AND cmp.branch_id IS NOT NULL
                GROUP BY cmp.branch_id
            ", [$weekStart, $weekEnd]);

            // Sort aggregated days by total_visits ASC (least busy first = bottom to top)
            usort($dayResults, fn($a, $b) => ($a->total_visits ?? 0) <=> ($b->total_visits ?? 0));
            $allDaysReport = array_map(fn($r) => (object)['day_name' => $r->day_name, 'total_visits' => (int) $r->total_visits], $dayResults);

            $branchOrder = ['CATFORD', 'TOOTING', 'SUTTON'];
            $branchTotalsMap = [];
            foreach ($branchTotals as $row) {
                $branchTotalsMap[$row->branch_id] = (int) $row->total_visits;
            }

            // Build per-branch report, each branch's days sorted least first; always include all branches
            $dayReportByBranch = [];
            foreach ($branchOrder as $branchId) {
                $dayReportByBranch[$branchId] = [
                    'branch_name' => $branchId,
                    'total_visits_week' => $branchTotalsMap[$branchId] ?? 0,
                    'data' => [],
                ];
            }
            foreach ($branchDayResults as $row) {
                $branchId = $row->branch_id;
                if (!isset($dayReportByBranch[$branchId])) {
                    $dayReportByBranch[$branchId] = [
                        'branch_name' => $branchId,
                        'total_visits_week' => $branchTotalsMap[$branchId] ?? 0,
                        'data' => [],
                    ];
                }
                $dayReportByBranch[$branchId]['data'][] = (object)[
                    'day_name' => $row->day_name,
                    'total_visits' => (int) $row->total_visits,
                ];
            }
            foreach ($dayReportByBranch as $branchId => &$branchData) {
                usort($branchData['data'], fn($a, $b) => $a->total_visits <=> $b->total_visits);
            }

            // Highest (busiest) day overall - last in ASC-sorted list
            $highestDay = !empty($allDaysReport) ? end($allDaysReport) : null;

            $emailData = [
                'allDaysReport' => $allDaysReport,
                'dayReportByBranch' => $dayReportByBranch,
                'branchOrder' => $branchOrder,
                'highestDay' => $highestDay,
                'weekStart' => $weekStart,
                'weekEnd' => $weekEnd,
            ];

            Mail::to(['support@neguinhomotors.co.uk', 'thiago@neguinhomotors.co.uk'])
                ->send(new WeeklyLeastBusiestDaysReportMail($emailData));

            Log::info('Weekly least busiest days report emailed successfully.');
            $this->info('Weekly least busiest days report emailed successfully.');

        } catch (\Exception $e) {
            Log::error('Error sending weekly least busiest days report', ['error' => $e->getMessage()]);
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
