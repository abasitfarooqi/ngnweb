<?php

namespace App\Services;

use App\Models\ClubMemberPurchase;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClubVisitStatsService
{
    /** @var array<int, string> */
    public const WEEKDAY_ORDER = [
        Carbon::MONDAY => 'Monday',
        Carbon::TUESDAY => 'Tuesday',
        Carbon::WEDNESDAY => 'Wednesday',
        Carbon::THURSDAY => 'Thursday',
        Carbon::FRIDAY => 'Friday',
        Carbon::SATURDAY => 'Saturday',
    ];

    /**
     * Unique club members with a purchase on the given calendar day (dashboard logic).
     */
    public function visitorsOnDate(Carbon $date): int
    {
        return (int) ClubMemberPurchase::query()
            ->whereDate('date', $date->toDateString())
            ->distinct('club_member_id')
            ->count('club_member_id');
    }

    /**
     * @return Collection<int, object{
     *     visit_date: string,
     *     day_name: string,
     *     unique: int,
     *     retaining: int,
     *     new: int
     * }>
     */
    public function dailyVisitorsBetween(Carbon $from, Carbon $to): Collection
    {
        $rows = DB::table('club_member_purchases as cmp')
            ->selectRaw('
                DATE(cmp.date) as visit_date,
                COUNT(DISTINCT cmp.club_member_id) as unique_visitors,
                COUNT(DISTINCT CASE
                    WHEN EXISTS (
                        SELECT 1 FROM club_member_purchases prior
                        WHERE prior.club_member_id = cmp.club_member_id
                        AND DATE(prior.date) < DATE(cmp.date)
                    ) THEN cmp.club_member_id
                END) as retaining_visitors
            ')
            ->whereBetween('cmp.date', [
                $from->copy()->startOfDay()->toDateTimeString(),
                $to->copy()->endOfDay()->toDateTimeString(),
            ])
            ->groupBy(DB::raw('DATE(cmp.date)'))
            ->orderBy('visit_date')
            ->get();

        return $rows->map(function ($row) {
            $date = Carbon::parse($row->visit_date);
            $unique = (int) $row->unique_visitors;
            $retaining = (int) $row->retaining_visitors;

            return (object) [
                'visit_date' => $date->toDateString(),
                'day_name' => self::WEEKDAY_ORDER[$date->dayOfWeek] ?? $date->format('l'),
                'unique' => $unique,
                'retaining' => $retaining,
                'new' => max(0, $unique - $retaining),
            ];
        });
    }

    /**
     * Distinct members in range, split by returning vs first-time in period.
     *
     * @return array{unique: int, retaining: int, new: int}
     */
    public function periodDistinctTotals(Carbon $from, Carbon $to, Carbon $retainingBefore): array
    {
        $fromTs = $from->copy()->startOfDay()->toDateTimeString();
        $toTs = $to->copy()->endOfDay()->toDateTimeString();
        $beforeTs = $retainingBefore->copy()->startOfDay()->toDateTimeString();

        $unique = (int) DB::table('club_member_purchases')
            ->whereBetween('date', [$fromTs, $toTs])
            ->distinct('club_member_id')
            ->count('club_member_id');

        $retaining = (int) DB::table('club_member_purchases as cmp')
            ->whereBetween('cmp.date', [$fromTs, $toTs])
            ->whereExists(function ($query) use ($beforeTs) {
                $query->selectRaw('1')
                    ->from('club_member_purchases as prior')
                    ->whereColumn('prior.club_member_id', 'cmp.club_member_id')
                    ->where('prior.date', '<', $beforeTs);
            })
            ->distinct('cmp.club_member_id')
            ->count('cmp.club_member_id');

        return [
            'unique' => $unique,
            'retaining' => $retaining,
            'new' => max(0, $unique - $retaining),
        ];
    }

    public function clubStartDate(): ?Carbon
    {
        $first = ClubMemberPurchase::query()->min('date');

        return $first ? Carbon::parse($first)->startOfDay() : null;
    }

    public function weekRangeForDate(Carbon $date): array
    {
        $monday = $date->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $saturday = $monday->copy()->addDays(5)->endOfDay();

        return [$monday, $saturday];
    }

    /**
     * @return array{
     *     week_start: string,
     *     week_end: string,
     *     range_end: string,
     *     days: Collection,
     *     daily_unique_sum: int,
     *     daily_retaining_sum: int,
     *     period: array{unique: int, retaining: int, new: int},
     *     busiest_days: array<int, object{day_name: string, visit_date: string, unique: int, retaining: int}>,
     *     quietest_days: array<int, object{day_name: string, visit_date: string, unique: int, retaining: int}>
     * }
     */
    public function weekReport(Carbon $weekMonday, ?Carbon $rangeEnd = null): array
    {
        $weekStart = $weekMonday->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekSaturday = $weekStart->copy()->addDays(5)->endOfDay();
        $effectiveEnd = $rangeEnd
            ? $rangeEnd->copy()->endOfDay()->min($weekSaturday)
            : $weekSaturday->copy();

        $daily = $this->dailyVisitorsBetween($weekStart, $effectiveEnd)
            ->keyBy('visit_date');

        $days = collect();
        $cursor = $weekStart->copy();
        while ($cursor->lte($effectiveEnd) && $cursor->lte($weekSaturday)) {
            $key = $cursor->toDateString();
            $row = $daily->get($key);

            $days->push((object) [
                'visit_date' => $key,
                'day_name' => self::WEEKDAY_ORDER[$cursor->dayOfWeek] ?? $cursor->format('l'),
                'unique' => (int) optional($row)->unique,
                'retaining' => (int) optional($row)->retaining,
                'new' => (int) optional($row)->new,
            ]);
            $cursor->addDay();
        }

        $dailyUniqueSum = (int) $days->sum('unique');
        $dailyRetainingSum = (int) $days->sum('retaining');
        $period = $this->periodDistinctTotals($weekStart, $effectiveEnd, $weekStart);

        $peak = (int) $days->max('unique');
        $busiestDays = $this->daysAtUniqueCount($days, $peak, requirePositive: true);

        $low = (int) $days->min('unique');
        $quietestDays = $this->daysAtUniqueCount($days, $low);

        return [
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekSaturday->toDateString(),
            'range_end' => $effectiveEnd->toDateString(),
            'days' => $days,
            'daily_unique_sum' => $dailyUniqueSum,
            'daily_retaining_sum' => $dailyRetainingSum,
            'period' => $period,
            'busiest_days' => $busiestDays,
            'quietest_days' => $quietestDays,
        ];
    }

    /**
     * @return array<int, object{day_name: string, visit_date: string, unique: int, retaining: int}>
     */
    private function daysAtUniqueCount(Collection $days, int $count, bool $requirePositive = false): array
    {
        if ($requirePositive && $count <= 0) {
            return [];
        }

        return $days
            ->filter(fn ($day) => $day->unique === $count)
            ->values()
            ->map(fn ($day) => (object) [
                'day_name' => $day->day_name,
                'visit_date' => $day->visit_date,
                'unique' => $day->unique,
                'retaining' => $day->retaining,
            ])
            ->all();
    }

    /**
     * @return Collection<int, array>
     */
    public function allWeeksBusiestReport(): Collection
    {
        $clubStart = $this->clubStartDate();
        if (! $clubStart) {
            return collect();
        }

        $weeks = collect();
        $weekCursor = $clubStart->copy()->startOfWeek(Carbon::MONDAY);
        $lastWeekMonday = Carbon::today()->startOfWeek(Carbon::MONDAY);

        while ($weekCursor->lte($lastWeekMonday)) {
            $rangeEnd = $weekCursor->isSameWeek(Carbon::today())
                ? Carbon::today()
                : $weekCursor->copy()->addDays(5);

            $weeks->push($this->weekReport($weekCursor, $rangeEnd));
            $weekCursor->addWeek();
        }

        return $weeks;
    }
}
