<?php

namespace App\Support;

class RegistrationMask
{
    public static function normalise(?string $regNo): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) ($regNo ?? '')));
    }

    public static function lastThree(?string $regNo): ?string
    {
        $reg = self::normalise($regNo);

        return $reg !== '' ? substr($reg, -3) : null;
    }

    /** Public-facing hint, e.g. ****XVK */
    public static function hint(?string $regNo): ?string
    {
        $last = self::lastThree($regNo);

        return $last !== null ? '****'.$last : null;
    }
}
