<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/** Safe path handling + directory listing for the hidden DO Spaces vault. */
final class SpacesVault
{
    public static function diskName(): string
    {
        return (string) config('spaces-vault.disk', 'spaces');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(self::diskName());
    }

    public static function configured(): bool
    {
        $disk = config('filesystems.disks.'.self::diskName(), []);

        return filled($disk['key'] ?? null)
            && filled($disk['secret'] ?? null)
            && filled($disk['bucket'] ?? null);
    }

    public static function routePath(): string
    {
        return trim((string) config('spaces-vault.path', '_vault/do-spaces'), '/');
    }

    /** Normalise and reject path traversal. Returns '' for bucket root. */
    public static function normalizePath(?string $path): string
    {
        $path = str_replace('\\', '/', (string) $path);
        $path = trim($path, '/');

        if ($path === '' || $path === '.') {
            return '';
        }

        if (str_contains($path, "\0")) {
            throw new InvalidArgumentException('Invalid path.');
        }

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                throw new InvalidArgumentException('Invalid path.');
            }
        }

        return $path;
    }

    /**
     * @return array{
     *     path: string,
     *     breadcrumbs: list<array{label: string, path: string}>,
     *     folders: list<array{name: string, path: string}>,
     *     files: list<array{name: string, path: string, size: int, modified: ?int, mime: ?string, previewable: bool}>
     * }
     */
    public static function listing(string $directory = '', ?string $filter = null): array
    {
        $directory = self::normalizePath($directory);
        $disk = self::disk();

        $folders = [];
        foreach ($disk->directories($directory) as $fullPath) {
            $name = basename($fullPath);
            if ($filter !== null && $filter !== '' && ! str_contains(strtolower($name), strtolower($filter))) {
                continue;
            }

            $folders[] = [
                'name' => $name,
                'path' => self::normalizePath($fullPath),
            ];
        }

        usort($folders, fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));

        $files = [];
        foreach ($disk->files($directory) as $fullPath) {
            $name = basename($fullPath);
            if ($filter !== null && $filter !== '' && ! str_contains(strtolower($name), strtolower($filter))) {
                continue;
            }

            $mime = null;
            $size = 0;
            $modified = null;

            try {
                $mime = $disk->mimeType($fullPath) ?: null;
            } catch (\Throwable) {
            }

            try {
                $size = (int) $disk->size($fullPath);
            } catch (\Throwable) {
            }

            try {
                $modified = (int) $disk->lastModified($fullPath);
            } catch (\Throwable) {
            }

            $files[] = [
                'name' => $name,
                'path' => self::normalizePath($fullPath),
                'size' => $size,
                'modified' => $modified ?: null,
                'mime' => $mime,
                'previewable' => self::isPreviewable($mime, $name),
            ];
        }

        usort($files, fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));

        return [
            'path' => $directory,
            'breadcrumbs' => self::breadcrumbs($directory),
            'folders' => $folders,
            'files' => $files,
        ];
    }

    /** @return list<array{label: string, path: string}> */
    public static function breadcrumbs(string $directory): array
    {
        $directory = self::normalizePath($directory);
        $crumbs = [['label' => 'Bucket root', 'path' => '']];

        if ($directory === '') {
            return $crumbs;
        }

        $parts = explode('/', $directory);
        $built = '';

        foreach ($parts as $part) {
            $built = $built === '' ? $part : $built.'/'.$part;
            $crumbs[] = ['label' => $part, 'path' => $built];
        }

        return $crumbs;
    }

    public static function isPreviewable(?string $mime, string $filename): bool
    {
        $mime = strtolower((string) $mime);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (str_starts_with($mime, 'image/')) {
            return true;
        }

        if ($mime === 'application/pdf' || $ext === 'pdf') {
            return true;
        }

        if (str_starts_with($mime, 'text/') || in_array($ext, ['txt', 'csv', 'json', 'xml', 'log', 'md'], true)) {
            return true;
        }

        return false;
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        if ($bytes < 1024 * 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 1).' MB';
        }

        return number_format($bytes / (1024 * 1024 * 1024), 2).' GB';
    }
}
