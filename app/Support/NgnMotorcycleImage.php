<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Full URLs for sales listing images (legacy data lives on the public NGN host).
 */
final class NgnMotorcycleImage
{
    public const REMOTE_BASE = 'https://neguinhomotors.co.uk';

    public static function urlForNewStock(?string $filePath): string
    {
        $path = trim((string) $filePath);
        if ($path === '') {
            return self::REMOTE_BASE.'/assets/img/no-image.png';
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        if (Str::startsWith($path, ['/storage/uploads/', '/storage/motorbikes/'])) {
            return self::REMOTE_BASE.$path;
        }

        return self::REMOTE_BASE.'/storage/motorbikes/'.ltrim($path, '/');
    }

    public static function urlForUsedSale(?string $imageOne): string
    {
        $path = trim((string) $imageOne);
        if ($path === '') {
            return self::REMOTE_BASE.'/assets/img/no-image.png';
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        if (Str::startsWith($path, ['/storage/', '/assets/'])) {
            return self::REMOTE_BASE.$path;
        }

        return self::REMOTE_BASE.'/storage/motorbikes/'.ltrim($path, '/');
    }
}
