<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\CustomerAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerPortalCredentialIssuer
{
    public static function normaliseEmail(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    public static function normalisePhone(?string $phone): string
    {
        $normalised = preg_replace('/\s+/', '', trim((string) $phone));

        return (string) preg_replace('/^\+44/', '0', $normalised);
    }

    /**
     * Create or reset portal login and return the plain temporary password.
     */
    public static function issue(Customer $customer): ?string
    {
        $email = self::normaliseEmail($customer->email);

        if ($email === '') {
            return null;
        }

        $temporaryPassword = (string) random_int(10000000, 99999999);

        CustomerAuth::query()->updateOrCreate(
            ['email' => $email],
            [
                'customer_id' => $customer->id,
                'password' => Hash::make($temporaryPassword),
            ]
        );

        $customer->forceFill(['is_register' => true])->save();

        return $temporaryPassword;
    }

    public static function issueAndNotify(Customer $customer): bool
    {
        $email = self::normaliseEmail($customer->email);
        $temporaryPassword = self::issue($customer);

        if ($temporaryPassword === null) {
            return false;
        }

        $phone = self::normalisePhone($customer->phone);
        $portalUrl = url('/login');
        $body = "Welcome to NGN customer portal.\n\nLogin email: {$email}\nTemporary password: {$temporaryPassword}\nPortal: {$portalUrl}\n\nPlease change your password after login.";

        try {
            Mail::raw(
                $body,
                fn ($message) => $message->to($email)->subject('Your NGN Portal Access Credentials')
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to send portal credentials email', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
        }

        if ($phone !== '') {
            try {
                app(\App\Http\Controllers\SMSController::class)->sendSms(
                    $phone,
                    "NGN Portal login\nEmail: {$email}\nPassword: {$temporaryPassword}\n{$portalUrl}"
                );
            } catch (\Throwable $e) {
                Log::warning('Failed to send portal credentials SMS', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return true;
    }
}
