<?php

namespace App\Console\Commands;

use App\Services\ClubVisitStatsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClubVisitReportCommand extends Command
{
    protected $signature = 'app:club-visit-report
                            {--date= : Any date in the week to report (Monday–Saturday totals for that week)}
                            {--all-weeks : Busiest day for every week since the first club visit}
                            {--json : Output machine-readable JSON}';

    protected $description = 'Club visitor stats: unique and retaining members per day (club_member_purchases)';

    public function handle(ClubVisitStatsService $stats): int
    {
        if ($this->option('all-weeks')) {
            return $this->reportAllWeeks($stats);
        }

        if ($dateOption = $this->option('date')) {
            return $this->reportWeekForDate($stats, Carbon::parse($dateOption));
        }

        return $this->reportCurrentWeekToToday($stats);
    }

    private function reportCurrentWeekToToday(ClubVisitStatsService $stats): int
    {
        $today = Carbon::today();
        $monday = $today->copy()->startOfWeek(Carbon::MONDAY);
        $report = $stats->weekReport($monday, $today);

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->info('Club visits — current week (Monday to today)');
        $this->line('Unique = distinct members that day. Retaining = returned members (visited before that date).');
        $this->newLine();
        $this->renderWeekTable($report);
        $this->renderWeekTotals($report);
        $this->renderExtremeDaysLine($report, 'busiest_days', 'Busiest day (unique)');
        $this->renderExtremeDaysLine($report, 'quietest_days', 'Quietest day (unique)');

        return self::SUCCESS;
    }

    private function reportWeekForDate(ClubVisitStatsService $stats, Carbon $date): int
    {
        [$monday, $saturday] = $stats->weekRangeForDate($date);
        $report = $stats->weekReport($monday, $saturday);

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Club visits — week %s to %s (Monday–Saturday)',
            $monday->format('d M Y'),
            $saturday->format('d M Y')
        ));
        $this->line('Unique = distinct members that day. Retaining = returned members (visited before that date).');
        $this->newLine();
        $this->renderWeekTable($report);
        $this->renderWeekTotals($report);
        $this->renderExtremeDaysLine($report, 'busiest_days', 'Busiest day (unique)');
        $this->renderExtremeDaysLine($report, 'quietest_days', 'Quietest day (unique)');

        return self::SUCCESS;
    }

    private function reportAllWeeks(ClubVisitStatsService $stats): int
    {
        $clubStart = $stats->clubStartDate();
        if (! $clubStart) {
            $this->warn('No club visits found in club_member_purchases.');

            return self::SUCCESS;
        }

        $weeks = $stats->allWeeksBusiestReport();

        if ($this->option('json')) {
            $this->line(json_encode([
                'club_start_date' => $clubStart->toDateString(),
                'weeks' => $weeks->values()->all(),
            ], JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->info('Busiest visit day per week (Monday–Saturday, since first club visit)');
        $this->line('Club day 1: '.$clubStart->format('d M Y'));
        $this->line('Unique = distinct members in week. Retaining = members who visited before that week started.');
        $this->newLine();

        $rows = [];
        foreach ($weeks as $report) {
            $period = $report['period'];
            $rows[] = [
                $report['week_start'].' → '.$report['range_end'],
                (string) $period['unique'],
                (string) $period['retaining'],
                (string) $period['new'],
                $this->formatExtremeDaysLabel($report['busiest_days']),
                $this->formatExtremeDaysLabel($report['quietest_days']),
            ];
        }

        $this->table(
            ['Week (Mon → end)', 'Unique', 'Retaining', 'New', 'Busiest day(s)', 'Quietest day(s)'],
            $rows
        );

        return self::SUCCESS;
    }

    private function renderWeekTable(array $report): void
    {
        $rows = $report['days']->map(fn ($day) => [
            $day->day_name,
            $day->visit_date,
            (string) $day->unique,
            (string) $day->retaining,
            (string) $day->new,
        ])->all();

        $this->table(['Day', 'Date', 'Unique', 'Retaining', 'New'], $rows);
    }

    private function renderWeekTotals(array $report): void
    {
        $period = $report['period'];
        $this->line(sprintf(
            'Daily sum — unique: %d, retaining: %d',
            $report['daily_unique_sum'],
            $report['daily_retaining_sum']
        ));
        $this->line(sprintf(
            'Period distinct — unique: %d, retaining: %d, new: %d',
            $period['unique'],
            $period['retaining'],
            $period['new']
        ));
    }

    private function renderExtremeDaysLine(array $report, string $key, string $label): void
    {
        $days = collect($report[$key] ?? []);
        if ($days->isEmpty()) {
            $this->line($label.': none recorded in range.');

            return;
        }

        $this->line($label.': '.$this->formatExtremeDaysLabel($days->all(), detailed: true));
    }

    /**
     * @param  array<int, object{day_name: string, visit_date: string, unique: int, retaining: int}>  $days
     */
    private function formatExtremeDaysLabel(array $days, bool $detailed = false): string
    {
        if ($days === []) {
            return '—';
        }

        return collect($days)->map(function ($day) use ($detailed) {
            if ($detailed) {
                return sprintf(
                    '%s %s (unique %d, retaining %d)',
                    $day->day_name,
                    Carbon::parse($day->visit_date)->format('d M Y'),
                    $day->unique,
                    $day->retaining
                );
            }

            return sprintf(
                '%s (unique %d, retaining %d)',
                $day->day_name,
                $day->unique,
                $day->retaining
            );
        })->implode(', ');
    }
}
