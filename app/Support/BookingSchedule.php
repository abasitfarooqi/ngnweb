<?php

namespace App\Support;

use Carbon\Carbon;

class BookingSchedule
{
    public static function isSunday(string|\DateTimeInterface|null $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return Carbon::parse($value)->isSunday();
    }

    /** @return list<string> ISO dates (Sundays) for Flux date-picker unavailable. */
    public static function unavailableSundays(int $weeksAhead = 104): array
    {
        $dates = [];
        $sunday = Carbon::today();

        if (! $sunday->isSunday()) {
            $sunday = Carbon::today()->next(Carbon::SUNDAY);
        }

        for ($i = 0; $i < $weeksAhead; $i++) {
            $dates[] = $sunday->copy()->addWeeks($i)->format('Y-m-d');
        }

        return $dates;
    }

    public static function unavailableSundaysCsv(int $weeksAhead = 104): string
    {
        return implode(',', self::unavailableSundays($weeksAhead));
    }

    /** Earliest selectable booking day (never a Sunday). */
    public static function minBookableDate(bool $fromTomorrow = false): string
    {
        $date = $fromTomorrow ? Carbon::tomorrow() : Carbon::today();

        while ($date->isSunday()) {
            $date = $date->copy()->addDay();
        }

        return $date->format('Y-m-d');
    }

    public static function defaultDateTimeLocal(): string
    {
        $dt = now()->addHour();

        while ($dt->isSunday()) {
            $dt = $dt->copy()->addDay()->setTime(9, 0);
        }

        return $dt->format('Y-m-d\TH:i');
    }

    public static function defaultPickUpDate(): string
    {
        return explode('T', self::defaultDateTimeLocal())[0];
    }

    public static function defaultPickUpTime(): string
    {
        return substr(explode('T', self::defaultDateTimeLocal())[1], 0, 5);
    }
}
