<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Full URLs for sales listing images.
 * Prefer local public disk when the file exists (new Flux uploads); otherwise
 * fall back to the legacy NGN remote host for older records.
 */
final class NgnMotorcycleImage
{
    public const REMOTE_BASE = 'https://neguinhomotors.co.uk';

    public static function urlForNewStock(?string $filePath): string
    {
        return self::resolve($filePath);
    }

    public static function urlForUsedSale(?string $imageOne): string
    {
        return self::resolve($imageOne);
    }

    /** Same path resolution for listing video files on the used_motorbikes disk. */
    public static function urlForMedia(?string $path): ?string
    {
        $trimmed = trim((string) $path);
        if ($trimmed === '') {
            return null;
        }

        $url = self::resolve($trimmed);

        return str_contains($url, 'no-image.png') ? null : $url;
    }

    private static function resolve(?string $filePath): string
    {
        $path = trim((string) $filePath);
        if ($path === '') {
            return self::REMOTE_BASE.'/assets/img/no-image.png';
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $relative = ltrim($path, '/');
        foreach (['storage/motorbikes/', 'storage/uploads/', 'motorbikes/', 'uploads/'] as $prefix) {
            if (Str::startsWith($relative, $prefix)) {
                $relative = substr($relative, strlen($prefix));
                break;
            }
        }

        if ($relative !== '' && Storage::disk('used_motorbikes')->exists($relative)) {
            return asset('storage/motorbikes/'.$relative);
        }

        if (Str::startsWith($path, ['/storage/', '/assets/'])) {
            return self::REMOTE_BASE.$path;
        }

        return self::REMOTE_BASE.'/storage/motorbikes/'.ltrim($path, '/');
    }
}
