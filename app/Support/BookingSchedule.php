<?php

namespace App\Support;

use App\Models\MOTBooking;
use Carbon\Carbon;

class BookingSchedule
{
    public const LEAD_MINUTES = 30;

    public static function leadMinutes(): int
    {
        return self::LEAD_MINUTES;
    }

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

    /** Earliest selectable booking day (never a Sunday). Same-day allowed. */
    public static function minBookableDate(bool $fromTomorrow = false): string
    {
        $date = $fromTomorrow ? Carbon::tomorrow() : Carbon::today();

        while ($date->isSunday()) {
            $date = $date->copy()->addDay();
        }

        return $date->format('Y-m-d');
    }

    /** Earliest bookable moment (now + lead minutes). */
    public static function earliestBookableDateTime(?Carbon $now = null): Carbon
    {
        return ($now ?? now())->copy()->addMinutes(self::LEAD_MINUTES);
    }

    /**
     * @param  array<string, string>  $slots  H:i => label
     * @return array<string, string>
     */
    public static function filterBookableSlots(array $slots, string $date): array
    {
        if ($date === '') {
            return $slots;
        }

        $day = Carbon::parse($date)->startOfDay();
        $earliest = self::earliestBookableDateTime();

        if ($day->lt($earliest->copy()->startOfDay())) {
            return [];
        }

        if (! $day->isSameDay($earliest)) {
            return $slots;
        }

        return array_filter(
            $slots,
            fn (string $label, string $value): bool => Carbon::parse($date.' '.$value)->gte($earliest),
            ARRAY_FILTER_USE_BOTH
        );
    }

    public static function isSlotBookable(string $date, string $time): bool
    {
        if ($date === '' || $time === '') {
            return false;
        }

        if (self::isSunday($date)) {
            return false;
        }

        try {
            $slotAt = Carbon::parse(trim($date.' '.$time));
        } catch (\Throwable) {
            return false;
        }

        if ($slotAt->lt(now()->startOfDay())) {
            return false;
        }

        return $slotAt->gte(self::earliestBookableDateTime());
    }

    public static function isDateTimeBookable(string|\DateTimeInterface $value): bool
    {
        try {
            $dt = Carbon::parse($value);
        } catch (\Throwable) {
            return false;
        }

        if ($dt->isSunday()) {
            return false;
        }

        return $dt->gte(self::earliestBookableDateTime());
    }

    /** Display label (12-hour) while stored values stay H:i. */
    public static function formatTimeAmPm(?string $time): string
    {
        if ($time === null || $time === '') {
            return '—';
        }

        $normalised = strlen($time) === 5 ? $time : Carbon::parse($time)->format('H:i');
        $slots = MOTBooking::motTimeSlots();

        if (isset($slots[$normalised])) {
            return $slots[$normalised];
        }

        try {
            return Carbon::parse($normalised)->format('g:i A');
        } catch (\Throwable) {
            return $time;
        }
    }

    public static function defaultDateTimeLocal(): string
    {
        $dt = self::earliestBookableDateTime();

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
