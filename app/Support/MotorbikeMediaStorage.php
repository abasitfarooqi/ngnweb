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
        $local = self::normalizeStoredPath($relativePath);

        if (self::spacesConfigured()) {
            $key = self::spacesKey($relativePath);

            if (self::spacesPut($key, $contents)) {
                return $key;
            }
        }

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

        if (self::spacesExists($key)) {
            self::deleteLocalCopies($path);

            return $key;
        }

        $contents = self::readLocalContents($path);

        if ($contents === null) {
            return $path;
        }

        if (self::spacesPut($key, $contents)) {
            self::deleteLocalCopies($path);

            return $key;
        }

        return $path;
    }

    public static function urlForPath(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return self::noImageUrl();
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $key = self::spacesKey($path);

        if (self::spacesConfigured() && self::spacesExists($key)) {
            return self::spacesUrl($key);
        }

        $localUrl = self::localUrlForPath($path);

        if ($localUrl !== null) {
            return $localUrl;
        }

        if (Str::startsWith($path, ['/storage/', '/assets/'])) {
            return self::siteBaseUrl().$path;
        }

        return self::siteBaseUrl().'/storage/'.self::spacesPrefix().ltrim(self::normalizeStoredPath($path), '/');
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $key = self::spacesKey($path);

        try {
            if (self::spacesConfigured() && self::spacesExists($key)) {
                Storage::disk('spaces')->delete($key);
            }
        } catch (\Throwable) {
        }

        self::deleteLocalCopies($path);
    }

    private static function noImageUrl(): string
    {
        return self::siteBaseUrl().'/assets/img/no-image.png';
    }

    private static function siteBaseUrl(): string
    {
        $url = rtrim((string) config('app.url'), '/');

        return $url !== '' ? $url : self::REMOTE_LEGACY_BASE;
    }

    private static function localUrlForPath(string $path): ?string
    {
        $local = self::normalizeStoredPath($path);

        if ($local !== '' && Storage::disk('used_motorbikes')->exists($local)) {
            return self::siteBaseUrl().'/storage/motorbikes/'.$local;
        }

        $publicPath = Str::startsWith($path, 'motorbikes/') ? $path : self::spacesPrefix().$local;

        if ($publicPath !== '' && Storage::disk('public')->exists($publicPath)) {
            return Storage::disk('public')->url($publicPath);
        }

        return null;
    }

    /** @param  resource|string  $contents */
    private static function spacesPut(string $key, $contents): bool
    {
        if ($key === '') {
            return false;
        }

        try {
            Storage::disk('spaces')->put($key, $contents, [
                'visibility' => 'public',
                'ACL' => 'public-read',
            ]);

            return self::spacesExists($key);
        } catch (\Throwable) {
            return false;
        }
    }

    private static function spacesExists(string $key): bool
    {
        if ($key === '') {
            return false;
        }

        try {
            return Storage::disk('spaces')->exists($key);
        } catch (\Throwable) {
            return false;
        }
    }

    private static function spacesUrl(string $key): string
    {
        try {
            return Storage::disk('spaces')->url($key);
        } catch (\Throwable) {
            return self::siteBaseUrl().'/'.ltrim($key, '/');
        }
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
