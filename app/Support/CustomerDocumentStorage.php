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
        // Documents are staged on local/public first.
        // A delayed queue job migrates to Spaces and only then removes local copy.
        if (! Storage::disk('public')->put($relativePath, $contents)) {
            throw new \RuntimeException('Could not store the file.');
        }
    }

    public static function moveToSpacesAndDeleteLocalIfSynced(string $path): bool
    {
        $normalised = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
        if ($normalised === '' || ! str_starts_with($normalised, 'customer-documents/')) {
            return false;
        }

        if (! self::spacesConfigured()) {
            return false;
        }

        $public = Storage::disk('public');
        $spaces = Storage::disk('spaces');

        try {
            if (! $public->exists($normalised)) {
                // Already removed locally; treat as moved if spaces has it.
                return $spaces->exists($normalised);
            }

            $contents = $public->get($normalised);
            $spaces->put($normalised, $contents, ['visibility' => 'public']);

            if ($spaces->exists($normalised)) {
                $public->delete($normalised);

                return true;
            }
        } catch (\Throwable $e) {
            \Log::warning('Customer document move to Spaces failed; local file retained.', [
                'path' => $normalised,
                'message' => $e->getMessage(),
            ]);
        }

        return false;
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $normalised = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
        if ($normalised === '') {
            return;
        }

        try {
            if (self::spacesConfigured() && Storage::disk('spaces')->exists($normalised)) {
                Storage::disk('spaces')->delete($normalised);
            }
        } catch (\Throwable) {
        }

        try {
            if (Storage::disk('public')->exists($normalised)) {
                Storage::disk('public')->delete($normalised);
            }
        } catch (\Throwable) {
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
            if (self::spacesConfigured() && Storage::disk('spaces')->exists($normalised)) {
                return Storage::disk('spaces')->url($normalised);
            }
        } catch (\Throwable) {
        }

        try {
            if (Storage::disk('public')->exists($normalised)) {
                return Storage::disk('public')->url($normalised);
            }
        } catch (\Throwable) {
        }

        if (self::spacesConfigured()) {
            try {
                return Storage::disk('spaces')->url($normalised);
            } catch (\Throwable) {
            }
        }

        return url('/storage/'.$normalised);
    }
}
