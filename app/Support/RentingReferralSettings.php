<?php

namespace App\Support;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

final class RentingReferralSettings
{
    public static function pointsPerQualifiedReferral(): int
    {
        $points = (int) self::value('points_per_qualified_referral', (int) config('renting_referrals.points_per_qualified_referral', 100));

        return max(1, $points);
    }

    public static function waitDays(): int
    {
        $days = (int) self::value('wait_days', (int) config('renting_referrals.wait_days', 14));

        return max(0, $days);
    }

    public static function earlyReleaseAllowed(): bool
    {
        return (bool) self::value('early_release_allowed', (bool) config('renting_referrals.early_release_allowed', true));
    }

    public static function approvalReportTo(): string
    {
        $email = trim((string) self::value('approval_report_to', (string) config('renting_referrals.approval_report_to', 'thiago@neguinhomotors.co.uk')));

        return $email !== '' ? $email : 'thiago@neguinhomotors.co.uk';
    }

    public static function transactionTypeName(): string
    {
        return (string) config('renting_referrals.transaction_type', 'Rental referral reward');
    }

    private static function value(string $key, mixed $default): mixed
    {
        if (! Schema::hasTable('system_settings')) {
            return $default;
        }

        $raw = SystemSetting::query()
            ->where('key', (string) config('renting_referrals.system_setting_key', 'renting_referrals'))
            ->value('value');

        if ($raw === null || $raw === '') {
            return $default;
        }

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
        if (! is_array($decoded) || ! array_key_exists($key, $decoded)) {
            return $default;
        }

        return $decoded[$key];
    }
}
