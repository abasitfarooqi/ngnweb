<?php

namespace App\Support\Communications;

use Illuminate\Support\Str;

final class CommunicationPreviewText
{
    public static function fromHtml(string $html, int $limit = 180): string
    {
        $stripped = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html) ?? $html;
        $stripped = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $stripped) ?? $stripped;
        $stripped = preg_replace('/<head\b[^>]*>.*?<\/head>/is', ' ', $stripped) ?? $stripped;
        $text = trim(html_entity_decode(strip_tags($stripped), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+/', ' ', $text) ?? '';

        $plain = self::dropCss($text);

        return $limit > 0 ? Str::limit($plain, $limit) : $plain;
    }

    public static function readable(?string $value, string $fallback = '', int $limit = 180): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return Str::limit($fallback, $limit);
        }

        $cleaned = trim(self::dropCss(self::fromHtml($value, 2000)));
        if ($cleaned === '' || self::looksLikeCss($cleaned)) {
            $cleaned = trim(self::dropCss($value));
        }

        if ($cleaned === '' || self::looksLikeCss($cleaned)) {
            return Str::limit($fallback, $limit);
        }

        return Str::limit($cleaned, $limit);
    }

    private static function looksLikeCss(string $text): bool
    {
        return str_contains($text, '-webkit-text-size-adjust')
            || str_contains($text, '-ms-text-size-adjust')
            || str_contains($text, 'mso-table-')
            || str_contains($text, 'mso-table-lspace')
            || (str_contains($text, '{') && str_contains($text, '}'));
    }

    private static function dropCss(string $text): string
    {
        if (! self::looksLikeCss($text)) {
            return $text;
        }

        $before = strstr($text, '{', true);
        if ($before === false) {
            return '';
        }

        $before = preg_replace(
            '/\b(?:body|html|table|td|th|a|img|p|div|span|h[1-6])(?:\s*,\s*(?:body|html|table|td|th|a|img|p|div|span|h[1-6]))*\s*$/i',
            '',
            $before,
        ) ?? $before;

        return trim($before, " \t\n\r\0\x0B,;");
    }
}
