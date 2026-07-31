<?php

namespace App\Support;

use App\Models\ClubMember;

class UkMobilePhone
{
    public static function sanitizeLiveInput(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (str_starts_with($digits, '44')) {
            $digits = '0'.substr($digits, 2);
        }

        return substr($digits, 0, 11);
    }

    public static function normalize(string $phone): string
    {
        return self::sanitizeLiveInput($phone);
    }

    public static function isValidMobile(string $phone): bool
    {
        return (bool) preg_match('/^07\d{9}$/', self::normalize($phone));
    }

    /** @return array<int, mixed> */
    public static function clubRegistrationRules(): array
    {
        return [
            'required',
            'string',
            'size:11',
            'regex:/^07\d{9}$/',
            function (string $attribute, mixed $value, \Closure $fail): void {
                $normalised = self::normalize((string) $value);

                if (ClubMember::query()->where('phone', $normalised)->exists()) {
                    $fail('You already have an account with this number. Please log in.');
                }
            },
        ];
    }

    /** @return array<string, string> */
    public static function validationMessages(): array
    {
        return [
            'phone.required' => 'Please enter your phone number.',
            'phone.size' => 'Please enter a valid UK mobile number — 11 digits starting with 07.',
            'phone.regex' => 'Please enter a valid UK mobile number starting with 07 (not 02). Symbols and +44 are not allowed.',
        ];
    }
}
