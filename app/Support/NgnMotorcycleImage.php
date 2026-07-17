<?php

namespace App\Support;

/** Full URLs for new-stock and used-sale bike listing images. */
final class NgnMotorcycleImage
{
    public const REMOTE_BASE = MotorbikeMediaStorage::REMOTE_LEGACY_BASE;

    public static function urlForNewStock(?string $filePath): string
    {
        return MotorbikeMediaStorage::urlForPath($filePath);
    }

    public static function urlForUsedSale(?string $imageOne): string
    {
        return MotorbikeMediaStorage::urlForPath($imageOne);
    }

    public static function urlForMedia(?string $path): ?string
    {
        $trimmed = trim((string) $path);
        if ($trimmed === '') {
            return null;
        }

        $url = MotorbikeMediaStorage::urlForPath($trimmed);

        return str_contains($url, 'no-image.png') ? null : $url;
    }
}
