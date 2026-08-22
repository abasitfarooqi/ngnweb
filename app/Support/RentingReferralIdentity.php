<?php

namespace App\Support;

final class RentingReferralIdentity
{
    public static function email(?string $email): ?string
    {
        $normalised = strtolower(trim((string) $email));

        return $normalised === '' ? null : $normalised;
    }

    public static function phone(?string $phone): ?string
    {
        $raw = trim((string) $phone);
        if ($raw === '' || self::isPlaceholder($raw)) {
            return null;
        }

        $normalised = UkMobilePhone::normalize($raw);

        return $normalised === '' ? null : $normalised;
    }

    public static function license(?string $license): ?string
    {
        $value = strtoupper(trim((string) $license));
        if ($value === '' || self::isPlaceholder($value)) {
            return null;
        }

        return preg_replace('/\s+/', '', $value) ?: null;
    }

    public static function looksLikeMobile(?string $value): bool
    {
        $phone = self::phone($value);

        return $phone !== null && UkMobilePhone::isValidMobile($phone);
    }

    public static function isPlaceholder(?string $value): bool
    {
        $normalised = strtolower(trim((string) $value));

        return $normalised === ''
            || $normalised === 'not provided'
            || $normalised === 'n/a'
            || $normalised === 'na'
            || $normalised === 'none';
    }

    public static function namesLookSimilar(string $left, string $right): bool
    {
        $a = self::compactName($left);
        $b = self::compactName($right);

        if ($a === '' || $b === '') {
            return false;
        }

        if ($a === $b) {
            return true;
        }

        similar_text($a, $b, $percent);

        return $percent >= 86;
    }

    public static function compactName(string $name): string
    {
        $value = strtolower(preg_replace('/[^a-z0-9]+/i', ' ', $name) ?? '');

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }
}
