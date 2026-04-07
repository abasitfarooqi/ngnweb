<?php

namespace App\Console\Commands;

use App\Mail\AnnualBusiestDaysReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAnnualBusiestDaysReport extends Command
{
    protected $signature = 'app:send-annual-busiest-days-report {--preview : Write HTML to storage and do not send email}';

    protected $description = 'Sends 12-month rolling report of club member purchase visits by day and by calendar month (all branches and per branch)';

    public function handle()
    {
        try {
            Log::info('Starting the annual busiest days report command.');

            $yearEnd = Carbon::today()->toDateString();
            $yearStart = Carbon::today()->subYear()->toDateString();

            $query = "
                select '--BRANCH--' as branch_id, '--DAY--' as period_type, '' as visit_count

                UNION ALL

                select cmp.branch_id, DAYNAME(cmp.date) as period_type, count(1) as visit_count
                from club_member_purchases cmp
                where cmp.date between ? and ?
                AND cmp.branch_id IS NOT NULL
                group by cmp.branch_id, DAYNAME(cmp.date)

                UNION ALL

                select '--BRANCH--' as branch_id, '--MONTH--' as period_type, '' as visit_count

                UNION ALL

                select cmp.branch_id, DATE_FORMAT(cmp.date, '%Y-%m') as period_type, count(1) as visit_count
                from club_member_purchases cmp
                where cmp.date between ? and ?
                AND cmp.branch_id IS NOT NULL
                group by cmp.branch_id, DATE_FORMAT(cmp.date, '%Y-%m')
            ";

            $results = DB::select($query, [$yearStart, $yearEnd, $yearStart, $yearEnd]);

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

                if ($isDaySection && ! $isMonthSection) {
                    $dayName = $periodType;
                    $dayData[$dayName] = ($dayData[$dayName] ?? 0) + (int) $visitCount;

                    if (! isset($dayReportByBranch[$branchId])) {
                        $dayReportByBranch[$branchId] = [
                            'branch_name' => $branchId,
                            'data' => [],
                        ];
                    }
                    $dayReportByBranch[$branchId]['data'][] = (object) [
                        'day_name' => $dayName,
                        'total_visits' => (int) $visitCount,
                    ];
                }

                if ($isMonthSection && ! $isDaySection) {
                    $monthKey = $periodType;
                    if (! preg_match('/^\d{4}-\d{2}$/', $monthKey)) {
                        continue;
                    }
                    $monthData[$monthKey] = ($monthData[$monthKey] ?? 0) + (int) $visitCount;

                    if (! isset($monthReportByBranch[$branchId])) {
                        $monthReportByBranch[$branchId] = [
                            'branch_name' => $branchId,
                            'data' => [],
                        ];
                    }
                    $monthReportByBranch[$branchId]['data'][] = (object) [
                        'month_key' => $monthKey,
                        'month_name' => Carbon::createFromFormat('Y-m', $monthKey)->format('F Y'),
                        'total_visits' => (int) $visitCount,
                    ];
                }
            }

            $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $allDaysReport = [];
            foreach ($dayOrder as $day) {
                $allDaysReport[] = (object) [
                    'day_name' => $day,
                    'total_visits' => $dayData[$day] ?? 0,
                ];
            }

            $allMonthsReport = [];
            $monthCursor = Carbon::parse($yearStart)->startOfMonth();
            $monthEnd = Carbon::parse($yearEnd)->startOfMonth();
            while ($monthCursor->lte($monthEnd)) {
                $monthKey = $monthCursor->format('Y-m');
                $allMonthsReport[] = (object) [
                    'month_key' => $monthKey,
                    'month_name' => $monthCursor->copy()->format('F Y'),
                    'total_visits' => $monthData[$monthKey] ?? 0,
                ];
                $monthCursor->addMonth();
            }

            foreach ($dayReportByBranch as &$branchData) {
                usort($branchData['data'], function ($a, $b) use ($dayOrder) {
                    $aIndex = array_search($a->day_name, $dayOrder);
                    $bIndex = array_search($b->day_name, $dayOrder);

                    return ($aIndex !== false ? $aIndex : 999) <=> ($bIndex !== false ? $bIndex : 999);
                });
            }
            unset($branchData);

            foreach ($monthReportByBranch as &$branchData) {
                usort($branchData['data'], function ($a, $b) {
                    return strcmp($a->month_key, $b->month_key);
                });
            }
            unset($branchData);

            $grandTotalVisits = array_sum(array_map(fn ($d) => $d->total_visits, $allDaysReport));

            $maxDayVisits = max(array_map(fn ($d) => $d->total_visits, $allDaysReport) ?: [0]);
            $busiestDayNames = [];
            if ((int) $maxDayVisits > 0) {
                foreach ($allDaysReport as $d) {
                    if ((int) $d->total_visits === (int) $maxDayVisits) {
                        $busiestDayNames[] = $d->day_name;
                    }
                }
            }

            $maxMonthVisits = max(array_map(fn ($m) => $m->total_visits, $allMonthsReport) ?: [0]);
            $busiestMonthLabels = [];
            if ((int) $maxMonthVisits > 0) {
                foreach ($allMonthsReport as $m) {
                    if ((int) $m->total_visits === (int) $maxMonthVisits) {
                        $busiestMonthLabels[] = $m->month_name;
                    }
                }
            }

            $branchInsights = [];
            foreach (array_unique(array_merge(array_keys($dayReportByBranch), array_keys($monthReportByBranch))) as $bid) {
                $bd = $dayReportByBranch[$bid]['data'] ?? [];
                $bm = $monthReportByBranch[$bid]['data'] ?? [];
                $branchInsights[$bid] = [
                    'busiest_days' => [],
                    'busiest_day_visits' => 0,
                    'busiest_months' => [],
                    'busiest_month_visits' => 0,
                    'branch_day_total' => 0,
                    'branch_month_total' => 0,
                ];
                if ($bd !== []) {
                    $maxBdv = max(array_map(fn ($r) => $r->total_visits, $bd));
                    if ((int) $maxBdv > 0) {
                        foreach ($bd as $r) {
                            if ((int) $r->total_visits === (int) $maxBdv) {
                                $branchInsights[$bid]['busiest_days'][] = $r->day_name;
                            }
                        }
                    }
                    $branchInsights[$bid]['busiest_day_visits'] = (int) $maxBdv;
                    $branchInsights[$bid]['branch_day_total'] = array_sum(array_map(fn ($r) => $r->total_visits, $bd));
                }
                if ($bm !== []) {
                    $maxBmv = max(array_map(fn ($r) => $r->total_visits, $bm));
                    if ((int) $maxBmv > 0) {
                        foreach ($bm as $r) {
                            if ((int) $r->total_visits === (int) $maxBmv) {
                                $branchInsights[$bid]['busiest_months'][] = $r->month_name;
                            }
                        }
                    }
                    $branchInsights[$bid]['busiest_month_visits'] = (int) $maxBmv;
                    $branchInsights[$bid]['branch_month_total'] = array_sum(array_map(fn ($r) => $r->total_visits, $bm));
                }
            }

            $emailData = [
                'allDaysReport' => $allDaysReport,
                'allMonthsReport' => $allMonthsReport,
                'dayReportByBranch' => $dayReportByBranch,
                'monthReportByBranch' => $monthReportByBranch,
                'yearStart' => $yearStart,
                'yearEnd' => $yearEnd,
                'insights' => [
                    'grand_total_visits' => $grandTotalVisits,
                    'busiest_days' => $busiestDayNames,
                    'busiest_day_visits' => (int) $maxDayVisits,
                    'busiest_months' => $busiestMonthLabels,
                    'busiest_month_visits' => (int) $maxMonthVisits,
                ],
                'branchInsights' => $branchInsights,
            ];

            if ($this->option('preview')) {
                $html = (new AnnualBusiestDaysReportMail($emailData))->render();
                $path = storage_path('app/busiest_report_preview.html');
                file_put_contents($path, $html);
                $this->info('Preview written to: '.$path);
                $this->newLine();
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Period', $yearStart.' to '.$yearEnd.' (inclusive dates)'],
                        ['Total visits (all branches)', number_format($grandTotalVisits)],
                        ['Busiest day(s)', implode(' & ', $busiestDayNames).' ('.number_format($maxDayVisits).')'],
                        ['Busiest month(s)', implode(' & ', $busiestMonthLabels).' ('.number_format($maxMonthVisits).')'],
                    ]
                );
                Log::info('Annual busiest days report preview generated (email not sent).');

                return self::SUCCESS;
            }

            Mail::to(['support@neguinhomotors.co.uk', 'thiago@neguinhomotors.co.uk'])
                ->send(new AnnualBusiestDaysReportMail($emailData));

            Log::info('Annual busiest days report emailed successfully.');
            $this->info('Annual busiest days report emailed successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Error sending annual busiest days report', ['error' => $e->getMessage()]);
            $this->error('An error occurred: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
