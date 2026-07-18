<?php

namespace App\Support;

use Mews\Purifier\Facades\Purifier;

final class MotorbikeAccessoriesHtml
{
    public static function sanitize(?string $raw): ?string
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        try {
            $clean = trim(Purifier::clean($raw, 'motorbike_accessories'));

            return $clean !== '' ? $clean : null;
        } catch (\Throwable) {
            $fallback = trim(strip_tags($raw, '<p><br><ul><ol><li><strong><b><em><i><span>'));

            return $fallback !== '' ? $fallback : null;
        }
    }
}
