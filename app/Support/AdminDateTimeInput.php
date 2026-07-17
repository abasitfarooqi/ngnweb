<?php

namespace App\Support;

use Carbon\Carbon;

final class AdminDateTimeInput
{
    /** Format for HTML datetime-local inputs. */
    public static function toLocal(mixed $value): string
    {
        if ($value === null || $value === '') {
            return now()->format('Y-m-d\TH:i');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d\TH:i');
        } catch (\Throwable) {
            return now()->format('Y-m-d\TH:i');
        }
    }

    /** Normalise datetime-local / date string to MySQL datetime. */
    public static function fromLocal(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse(str_replace('T', ' ', trim($value)))->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }

    public static function parseStart(mixed $value): Carbon
    {
        if ($value === null || $value === '') {
            return now();
        }

        return Carbon::parse(str_replace('T', ' ', (string) $value));
    }
}
