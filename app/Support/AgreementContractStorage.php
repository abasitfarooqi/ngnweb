<?php

namespace App\Support;

use App\Jobs\ArchiveAgreementPdfToSpacesJob;
use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/** Signed rental/finance PDFs: email on sign, then private DO Spaces after delay. */
final class AgreementContractStorage
{
    public static function spacesConfigured(): bool
    {
        $disk = config('filesystems.disks.spaces', []);

        return filled($disk['key'] ?? null)
            && filled($disk['secret'] ?? null)
            && filled($disk['bucket'] ?? null);
    }

    public static function archiveDelayMinutes(): int
    {
        return max(1, (int) config('agreement.archive_delay_minutes', 20));
    }

    public static function spacesPrefix(): string
    {
        return trim((string) config('agreement.spaces_archive_prefix', 'agreement-archives/'), '/').'/';
    }

    public static function normalizePath(?string $path): string
    {
        return ltrim(str_replace(['storage/', 'public/'], '', (string) $path), '/');
    }

    public static function isAgreementPdfPath(string $path): bool
    {
        $path = self::normalizePath($path);

        if ($path === '') {
            return false;
        }

        if (str_starts_with($path, self::spacesPrefix())) {
            return true;
        }

        if (! str_starts_with($path, 'customers/') || str_contains($path, '/documents/')) {
            return false;
        }

        return str_ends_with(strtolower($path), '.pdf');
    }

    public static function spacesKey(string $normalizedPublicPath): string
    {
        $normalizedPublicPath = self::normalizePath($normalizedPublicPath);

        if (str_starts_with($normalizedPublicPath, self::spacesPrefix())) {
            return $normalizedPublicPath;
        }

        return self::spacesPrefix().$normalizedPublicPath;
    }

    /** No URL once archived or marked private — DO console only. */
    public static function appUrl(?string $path, bool $sentPrivate = false): ?string
    {
        if (! $path || $sentPrivate) {
            return null;
        }

        $normalised = self::normalizePath($path);

        if ($normalised === '' || ! self::isAgreementPdfPath($normalised)) {
            return null;
        }

        if (str_starts_with($normalised, self::spacesPrefix())) {
            return null;
        }

        try {
            if (Storage::disk('public')->exists($normalised)) {
                return Storage::disk('public')->url($normalised);
            }
        } catch (\Throwable) {
        }

        return null;
    }

    public static function scheduleArchive(CustomerAgreement|CustomerContract $record): void
    {
        $path = self::normalizePath($record->file_path ?? '');

        if ($path === '' || ! self::isAgreementPdfPath($path)) {
            return;
        }

        if ($record->sent_private && str_starts_with($path, self::spacesPrefix())) {
            return;
        }

        ArchiveAgreementPdfToSpacesJob::dispatch($record::class, (int) $record->id, $path)
            ->delay(now()->addMinutes(self::archiveDelayMinutes()));
    }

    public static function archiveRecord(string $modelClass, int $recordId, string $expectedPath): bool
    {
        if (! in_array($modelClass, [CustomerAgreement::class, CustomerContract::class], true)) {
            return false;
        }

        /** @var CustomerAgreement|CustomerContract|null $record */
        $record = $modelClass::query()->find($recordId);

        if (! $record) {
            return false;
        }

        $expectedPath = self::normalizePath($expectedPath);
        $currentPath = self::normalizePath($record->file_path ?? '');

        if ($expectedPath === '' || $currentPath !== $expectedPath) {
            return false;
        }

        if ($record->sent_private && str_starts_with($currentPath, self::spacesPrefix())) {
            return true;
        }

        if (! self::spacesConfigured()) {
            Log::warning('Agreement PDF archive skipped — DO Spaces not configured.', [
                'model' => $modelClass,
                'id' => $recordId,
                'path' => $expectedPath,
            ]);

            return self::moveToLocalPrivate($record, $expectedPath);
        }

        $public = Storage::disk('public');
        $private = Storage::disk('private');
        $spaces = Storage::disk('spaces');
        $spacesKey = self::spacesKey($expectedPath);

        try {
            if ($spaces->exists($spacesKey)) {
                self::deleteLocalCopies($public, $private, $expectedPath);
                $record->update([
                    'file_path' => $spacesKey,
                    'sent_private' => true,
                ]);

                return true;
            }

            if (! $public->exists($expectedPath)) {
                if ($private->exists($expectedPath)) {
                    $contents = $private->get($expectedPath);
                } else {
                    Log::warning('Agreement PDF missing locally before Spaces archive.', [
                        'model' => $modelClass,
                        'id' => $recordId,
                        'path' => $expectedPath,
                    ]);

                    return false;
                }
            } else {
                $contents = $public->get($expectedPath);
            }

            $spaces->put($spacesKey, $contents, ['visibility' => 'private']);

            if (! $spaces->exists($spacesKey)) {
                return false;
            }

            self::deleteLocalCopies($public, $private, $expectedPath);

            $record->update([
                'file_path' => $spacesKey,
                'sent_private' => true,
            ]);

            Log::info('Agreement PDF archived to private DO Spaces.', [
                'model' => $modelClass,
                'id' => $recordId,
                'spaces_key' => $spacesKey,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Agreement PDF archive to Spaces failed.', [
                'model' => $modelClass,
                'id' => $recordId,
                'path' => $expectedPath,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private static function moveToLocalPrivate(Model $record, string $expectedPath): bool
    {
        $public = Storage::disk('public');
        $private = Storage::disk('private');

        if (! $public->exists($expectedPath)) {
            return false;
        }

        try {
            $private->makeDirectory(dirname($expectedPath));
            $private->put($expectedPath, $public->get($expectedPath));
            $public->delete($expectedPath);
            $record->update(['sent_private' => true]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Agreement PDF local private fallback failed.', [
                'id' => $record->getKey(),
                'path' => $expectedPath,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private static function deleteLocalCopies($public, $private, string $path): void
    {
        try {
            if ($public->exists($path)) {
                $public->delete($path);
            }
        } catch (\Throwable) {
        }

        try {
            if ($private->exists($path)) {
                $private->delete($path);
            }
        } catch (\Throwable) {
        }
    }
}
