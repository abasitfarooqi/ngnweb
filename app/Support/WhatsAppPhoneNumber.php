<?php

namespace App\Support;

final class WhatsAppPhoneNumber
{
    public static function normalize(string $phone): ?string
    {
        $number = trim($phone);
        if ($number === '') {
            return null;
        }

        $number = preg_replace('/[^0-9+]/', '', $number);
        if (str_starts_with($number, '00')) {
            $number = substr($number, 2);
        } elseif (str_starts_with($number, '+')) {
            $number = substr($number, 1);
        }

        if (str_starts_with($number, '0')) {
            $number = '44'.substr($number, 1);
        }

        return preg_match('/^[0-9]{8,15}$/', $number) === 1 ? $number : null;
    }
}
