<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Generate QR code as PNG or SVG data URL using bacon/bacon-qr-code (Fortify dependency).
 * Prefers GD for PNG; falls back to SVG if GD is unavailable.
 */
class QrCodeGenerator
{
    /**
     * Generate a QR code and return as a data URL (PNG if GD available, else SVG).
     */
    public static function dataUrl(string $content, int $size = 200): string
    {
        if (extension_loaded('gd') && function_exists('gd_info')) {
            return self::pngDataUrl($content, $size);
        }

        return self::svgDataUrl($content, $size);
    }

    /**
     * Generate PNG data URL. Requires GD extension.
     */
    public static function pngDataUrl(string $content, int $size = 200): string
    {
        $renderer = new \BaconQrCode\Renderer\GDLibRenderer($size, 4, 'png');
        $writer = new Writer($renderer);
        $png = $writer->writeString($content);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * Generate SVG data URL. No extra extension required.
     */
    public static function svgDataUrl(string $content, int $size = 200): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 4),
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($content);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
