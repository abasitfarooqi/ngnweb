<?php

namespace App\Support\Communications;

final class CommunicationStaffRedactor
{
    public static function html(?string $html): string
    {
        $html = (string) $html;
        if ($html === '') {
            return '';
        }

        $html = preg_replace(
            '/(>(?:password|passkey|passcode|pin)<\/t[dh]>\s*<t[dh][^>]*>)([^<]+)/i',
            '$1[hidden]',
            $html
        ) ?? $html;

        return preg_replace(
            '/\b((?:password|passkey|passcode|pin)\s*[:：]\s*)([^\s<]+)/i',
            '$1[hidden]',
            $html
        ) ?? $html;
    }

    public static function text(?string $text): string
    {
        $text = (string) $text;
        if ($text === '') {
            return '';
        }

        return preg_replace(
            '/\b((?:password|passkey|passcode|pin)\s*[:：]\s*)(\S+)/i',
            '$1[hidden]',
            $text
        ) ?? $text;
    }
}
