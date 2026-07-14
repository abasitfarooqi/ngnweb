<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

/**
 * Persists a signature-pad base64 payload as an image file for use in agreement/contract PDFs.
 *
 * Centralised so every signing endpoint gets the same sanitised filename, correct
 * extension (matched to the actual image bytes, not assumed), and a loud failure
 * (instead of a silently missing signature) when the disk write does not succeed —
 * this was previously failing quietly on the server while working locally.
 */
final class SignatureUploadStore
{
    /**
     * @param string $subDir Optional relative folder (e.g. 'employee') to prefix the stored path with.
     * @return string The relative path on $disk (including $subDir, if given) — ready to hand
     *                 straight to AgreementPdfViewAssets::signatureSrc().
     */
    public static function store(string $base64Image, string $firstName, string $lastName, string $disk = 'public', string $subDir = ''): string
    {
        $parts = explode(';', $base64Image, 2);
        $meta = $parts[0] ?? '';
        $payload = $parts[1] ?? '';
        @[, $fileData] = explode(',', $payload, 2);

        $binary = base64_decode((string) $fileData, true);
        if ($binary === false || $binary === '') {
            throw new InvalidArgumentException('Invalid signature payload.');
        }

        $isPng = str_contains(strtolower($meta), 'image/png') || str_starts_with($binary, "\x89PNG");
        $ext = $isPng ? 'png' : 'jpg';
        $safeFirst = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim($firstName)) ?: 'customer';
        $safeLast = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim($lastName)) ?: 'sign';
        $fileName = $safeFirst.'-'.$safeLast.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.$ext;
        $relativePath = $subDir !== '' ? trim($subDir, '/').'/'.$fileName : $fileName;

        $saved = Storage::disk($disk)->put($relativePath, $binary);
        if (! $saved) {
            Log::error("SignatureUploadStore: failed to write signature '{$relativePath}' to disk '{$disk}'.");

            throw new RuntimeException("Failed to save signature to the '{$disk}' disk.");
        }

        return $relativePath;
    }
}
