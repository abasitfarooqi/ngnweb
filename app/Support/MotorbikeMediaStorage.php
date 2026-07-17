<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/** Sale / new-stock bike images — public DO Spaces with local fallback. */
final class MotorbikeMediaStorage
{
    public const REMOTE_LEGACY_BASE = 'https://neguinhomotors.co.uk';

    public static function spacesPrefix(): string
    {
        return trim((string) config('motorbike-media.spaces_prefix', 'motorbikes/'), '/').'/';
    }

    public static function spacesConfigured(): bool
    {
        $disk = config('filesystems.disks.spaces', []);

        return filled($disk['key'] ?? null)
            && filled($disk['secret'] ?? null)
            && filled($disk['bucket'] ?? null);
    }

    public static function normalizeStoredPath(?string $path): string
    {
        $path = trim(str_replace('\\', '/', (string) $path), '/');

        foreach (['storage/motorbikes/', 'storage/uploads/', 'public/motorbikes/', 'motorbikes/', 'uploads/'] as $prefix) {
            if (Str::startsWith($path, $prefix)) {
                $path = substr($path, strlen($prefix));
                break;
            }
        }

        return ltrim($path, '/');
    }

    public static function spacesKey(string $path): string
    {
        $path = self::normalizeStoredPath($path);

        if ($path === '') {
            return '';
        }

        if (Str::startsWith($path, self::spacesPrefix())) {
            return $path;
        }

        return self::spacesPrefix().$path;
    }

    /**
     * @param  UploadedFile|TemporaryUploadedFile  $file
     */
    public static function putUploadedFile(UploadedFile|TemporaryUploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'bike_'.uniqid('', true).'.'.$extension;

        return self::put($filename, $file->get());
    }

    /** @param  resource|string  $contents */
    public static function put(string $relativePath, $contents): string
    {
        $key = self::spacesKey($relativePath);

        if (self::spacesConfigured()) {
            Storage::disk('spaces')->put($key, $contents, ['visibility' => 'public']);

            return $key;
        }

        $local = self::normalizeStoredPath($relativePath);
        Storage::disk('used_motorbikes')->put($local, $contents);

        return $local;
    }

    /** Move a local-only file to Spaces; returns the path to store in DB. */
    public static function promoteLocalToSpaces(string $path): string
    {
        if ($path === '' || ! self::spacesConfigured()) {
            return $path;
        }

        $key = self::spacesKey($path);

        if (Storage::disk('spaces')->exists($key)) {
            self::deleteLocalCopies($path);

            return $key;
        }

        $contents = self::readLocalContents($path);

        if ($contents === null) {
            return $path;
        }

        Storage::disk('spaces')->put($key, $contents, ['visibility' => 'public']);

        if (Storage::disk('spaces')->exists($key)) {
            self::deleteLocalCopies($path);

            return $key;
        }

        return $path;
    }

    public static function urlForPath(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return self::REMOTE_LEGACY_BASE.'/assets/img/no-image.png';
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $key = self::spacesKey($path);

        if (self::spacesConfigured()) {
            try {
                if (Storage::disk('spaces')->exists($key)) {
                    return Storage::disk('spaces')->url($key);
                }
            } catch (\Throwable) {
            }

            try {
                return Storage::disk('spaces')->url($key);
            } catch (\Throwable) {
            }
        }

        $local = self::normalizeStoredPath($path);

        if ($local !== '' && Storage::disk('used_motorbikes')->exists($local)) {
            return asset('storage/motorbikes/'.$local);
        }

        $publicPath = Str::startsWith($path, 'motorbikes/') ? $path : self::spacesPrefix().$local;

        if ($publicPath !== '' && Storage::disk('public')->exists($publicPath)) {
            return Storage::disk('public')->url($publicPath);
        }

        if (Str::startsWith($path, ['/storage/', '/assets/'])) {
            return self::REMOTE_LEGACY_BASE.$path;
        }

        return self::REMOTE_LEGACY_BASE.'/storage/'.self::spacesPrefix().ltrim($local, '/');
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $key = self::spacesKey($path);

        try {
            if (self::spacesConfigured() && Storage::disk('spaces')->exists($key)) {
                Storage::disk('spaces')->delete($key);
            }
        } catch (\Throwable) {
        }

        self::deleteLocalCopies($path);
    }

    private static function readLocalContents(string $path): ?string
    {
        $local = self::normalizeStoredPath($path);

        if ($local !== '' && Storage::disk('used_motorbikes')->exists($local)) {
            return Storage::disk('used_motorbikes')->get($local);
        }

        foreach ([self::spacesKey($path), 'motorbikes/'.$local, $path] as $candidate) {
            if ($candidate !== '' && Storage::disk('public')->exists($candidate)) {
                return Storage::disk('public')->get($candidate);
            }
        }

        return null;
    }

    private static function deleteLocalCopies(string $path): void
    {
        $local = self::normalizeStoredPath($path);

        try {
            if ($local !== '' && Storage::disk('used_motorbikes')->exists($local)) {
                Storage::disk('used_motorbikes')->delete($local);
            }
        } catch (\Throwable) {
        }

        foreach ([self::spacesKey($path), 'motorbikes/'.$local, $path] as $candidate) {
            try {
                if ($candidate !== '' && Storage::disk('public')->exists($candidate)) {
                    Storage::disk('public')->delete($candidate);
                }
            } catch (\Throwable) {
            }
        }
    }
}
