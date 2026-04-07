<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class CustomerDocumentStorage
{
    public static function spacesConfigured(): bool
    {
        $disk = config('filesystems.disks.spaces', []);

        return filled($disk['key'] ?? null)
            && filled($disk['secret'] ?? null)
            && filled($disk['bucket'] ?? null);
    }

    /**
     * @param  resource|string  $contents
     */
    public static function put(string $relativePath, $contents): void
    {
        $relativePath = ltrim($relativePath, '/');

        if (self::spacesConfigured()) {
            try {
                if (Storage::disk('spaces')->put($relativePath, $contents)) {
                    return;
                }
            } catch (\Throwable $e) {
                \Log::warning('Customer document upload to Spaces failed; using public disk.', [
                    'path' => $relativePath,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if (! Storage::disk('public')->put($relativePath, $contents)) {
            throw new \RuntimeException('Could not store the file.');
        }
    }

    /**
     * Public URL for a stored customer-documents path (local copy wins, then Spaces).
     */
    public static function urlForPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalised = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
        if ($normalised === '' || ! str_starts_with($normalised, 'customer-documents/')) {
            return null;
        }

        try {
            if (Storage::disk('public')->exists($normalised)) {
                return Storage::disk('public')->url($normalised);
            }
        } catch (\Throwable) {
        }

        try {
            return Storage::disk('spaces')->url($normalised);
        } catch (\Throwable) {
            return url('/storage/'.$normalised);
        }
    }
}
