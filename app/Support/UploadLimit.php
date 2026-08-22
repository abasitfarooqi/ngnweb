<?php

namespace App\Support;

class UploadLimit
{
    public const APP_MAX_BYTES = 512 * 1024 * 1024;

    public static function maxBytes(): int
    {
        $upload = self::iniBytes((string) ini_get('upload_max_filesize'));
        $post = self::iniBytes((string) ini_get('post_max_size'));
        $postHeadroom = max(0, $post - (2 * 1024 * 1024));

        $ceiling = min(
            self::APP_MAX_BYTES,
            $upload > 0 ? $upload : self::APP_MAX_BYTES,
            $postHeadroom > 0 ? $postHeadroom : self::APP_MAX_BYTES,
        );

        return max(1024 * 1024, $ceiling);
    }

    public static function maxKilobytes(): int
    {
        return (int) floor(self::maxBytes() / 1024);
    }

    public static function label(): string
    {
        return ((int) floor(self::maxBytes() / (1024 * 1024))).' MB';
    }

    private static function iniBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '' || $value === '0') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
