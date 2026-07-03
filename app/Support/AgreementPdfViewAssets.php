<?php

namespace App\Support;

/**
 * Resolves logo and watermark for agreement PDF Blade views.
 * Logo: small data URI. Watermark: local absolute path (never HTTP — avoids
 * php artisan serve deadlock and huge base64 memory use on repeat backgrounds).
 */
final class AgreementPdfViewAssets
{
    private const TRANSPARENT_PIXEL_PNG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    /** @var array{agreementPdfLogoSrc: string, agreementPdfWatermarkSrc: string, agreementSigningWatermarkSrc: string}|null */
    private static ?array $cached = null;

    /**
     * @return array{agreementPdfLogoSrc: string, agreementPdfWatermarkSrc: string, agreementSigningWatermarkSrc: string}
     */
    public static function composerVariables(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $brand = config('agreement.brand', []);

        $logoPath = self::publicPath((string) ($brand['pdf_logo_local'] ?? ''));
        $logoRemote = (string) ($brand['pdf_logo_remote'] ?? '');

        $wmRel = (string) ($brand['pdf_watermark_local'] ?? '');
        $wmPath = $wmRel !== '' ? self::publicPath($wmRel) : '';
        $wmRemote = (string) ($brand['pdf_watermark_remote'] ?? '');

        $logoSrc = self::imageToDataUri($logoPath)
            ?? self::tryFetchImageDataUri($logoRemote)
            ?? $logoRemote;

        if ($logoSrc === '') {
            $logoSrc = self::TRANSPARENT_PIXEL_PNG;
        }

        $wmPdfSrc = self::localPathForDompdf($wmPath)
            ?? self::tryFetchImageDataUri($wmRemote)
            ?? self::TRANSPARENT_PIXEL_PNG;

        $wmSigningSrc = $wmRel !== ''
            ? asset($wmRel)
            : ($wmRemote !== '' ? $wmRemote : self::TRANSPARENT_PIXEL_PNG);

        return self::$cached = [
            'agreementPdfLogoSrc' => $logoSrc,
            'agreementPdfWatermarkSrc' => $wmPdfSrc,
            'agreementSigningWatermarkSrc' => $wmSigningSrc,
        ];
    }

    private static function publicPath(string $relative): string
    {
        $relative = ltrim($relative, '/');

        return $relative === '' ? '' : public_path($relative);
    }

    private static function localPathForDompdf(string $absolutePath): ?string
    {
        if ($absolutePath === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        $resolved = realpath($absolutePath);

        return $resolved !== false
            ? str_replace('\\', '/', $resolved)
            : str_replace('\\', '/', $absolutePath);
    }

    private static function imageToDataUri(string $absolutePath): ?string
    {
        if ($absolutePath === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        $size = filesize($absolutePath);
        if ($size === false || $size > 256000) {
            return null;
        }

        $binary = @file_get_contents($absolutePath);
        if ($binary === false || $binary === '') {
            return null;
        }

        $mime = @mime_content_type($absolutePath) ?: '';
        $lower = strtolower($absolutePath);
        if ($mime === 'image/svg+xml' || str_ends_with($lower, '.svg')) {
            return 'data:image/svg+xml;base64,'.base64_encode($binary);
        }

        if ($mime === '' || ! str_starts_with($mime, 'image/')) {
            $mime = 'image/png';
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }

    private static function tryFetchImageDataUri(string $url): ?string
    {
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $ctx = stream_context_create([
            'http' => [
                'timeout' => 4,
                'follow_location' => 1,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $binary = @file_get_contents($url, false, $ctx);
        if ($binary === false || $binary === '') {
            return null;
        }

        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        $mime = 'image/png';
        if (str_ends_with(strtolower($path), '.jpg') || str_ends_with(strtolower($path), '.jpeg')) {
            $mime = 'image/jpeg';
        }
        if (str_ends_with(strtolower($path), '.gif')) {
            $mime = 'image/gif';
        }
        if (str_ends_with(strtolower($path), '.webp')) {
            $mime = 'image/webp';
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }
}
