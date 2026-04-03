<?php

namespace App\Support;

/**
 * Resolves logo and watermark for agreement PDF Blade views so Dompdf receives
 * embeddable data URIs (local paths and remote https often fail otherwise).
 */
final class AgreementPdfViewAssets
{
    private const TRANSPARENT_PIXEL_PNG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    /**
     * @return array{agreementPdfLogoSrc: string, agreementPdfWatermarkSrc: string}
     */
    public static function composerVariables(): array
    {
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

        $wmSrc = self::imageToDataUri($wmPath)
            ?? self::tryFetchImageDataUri($wmRemote)
            ?? self::syntheticPngWatermarkDataUri()
            ?? self::TRANSPARENT_PIXEL_PNG;

        return [
            'agreementPdfLogoSrc' => $logoSrc,
            'agreementPdfWatermarkSrc' => $wmSrc,
        ];
    }

    private static function publicPath(string $relative): string
    {
        $relative = ltrim($relative, '/');

        return $relative === '' ? '' : public_path($relative);
    }

    private static function imageToDataUri(string $absolutePath): ?string
    {
        if ($absolutePath === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
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

    /**
     * Seamlessly tileable 256×256 PNG so repeat has no gaps at page edges.
     */
    private static function syntheticPngWatermarkDataUri(): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $s = 256;
        $im = imagecreatetruecolor($s, $s);
        if ($im === false) {
            return null;
        }

        imagesavealpha($im, true);
        $clear = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefill($im, 0, 0, $clear);
        $stroke = imagecolorallocatealpha($im, 130, 138, 150, 110);

        for ($y = 0; $y <= $s; $y += 32) {
            imageline($im, 0, $y, $s, $y, $stroke);
        }

        for ($x = 0; $x <= $s; $x += 32) {
            imageline($im, $x, 0, $x, $s, $stroke);
        }

        ob_start();
        imagepng($im, null, 9);
        $png = ob_get_clean();
        imagedestroy($im);

        if ($png === false || $png === '') {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($png);
    }
}
