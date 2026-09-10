<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\CustomerAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

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
                'is_active' => true,
            ]
        );

        $customer->forceFill(['is_register' => true, 'is_active' => true])->save();

        return $temporaryPassword;
    }

    public static function issueAndNotify(Customer $customer, string $channel = 'both'): bool
    {
        $email = self::normaliseEmail($customer->email);
        $temporaryPassword = self::issue($customer);

        if ($temporaryPassword === null) {
            return false;
        }

        $phone = self::normalisePhone($customer->phone);
        $portalUrl = url('/login');

        if (in_array($channel, ['both', 'email'], true)) {
            try {
                Mail::to($email)->send(new \App\Mail\PortalCredentialsMail($email, $temporaryPassword, $portalUrl));
            } catch (\Throwable $e) {
                Log::warning('Failed to send portal credentials email', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // One SMS only — credentials, no URL (email already has the portal link).
        if ($phone !== '' && in_array($channel, ['both', 'sms'], true)) {
            $smsLockKey = 'portal_creds_sms_'.$customer->id;
            if (! cache()->add($smsLockKey, 1, now()->addSeconds(45))) {
                Log::info('Skipped duplicate portal credentials SMS', [
                    'customer_id' => $customer->id,
                ]);
            } else {
                try {
                    app(\App\Http\Controllers\SMSController::class)->sendSms(
                        $phone,
                        "NGN Portal login\nEmail: {$email}\nPassword: {$temporaryPassword}"
                    );
                } catch (\Throwable $e) {
                    cache()->forget($smsLockKey);
                    Log::warning('Failed to send portal credentials SMS', [
                        'customer_id' => $customer->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return true;
    }

    /**
     * Create a short-lived password-reset token without exposing the existing password.
     */
    public static function sendResetLink(Customer $customer, string $channel = 'email'): bool
    {
        $auth = $customer->customerAuth;
        $email = self::normaliseEmail($customer->email ?: $auth?->email);

        if (! $auth || $email === '') {
            return false;
        }

        $auth->forceFill(['email' => $email])->save();
        $token = Password::broker('customers')->createToken($auth);
        $resetUrl = url('/reset-password/'.$token.'?email='.urlencode($email));

        if (in_array($channel, ['email', 'both'], true)) {
            try {
                Mail::to($email)->send(new \App\Mail\PortalPasswordResetMail($email, $resetUrl));
            } catch (\Throwable $e) {
                Log::warning('Failed to send portal password reset email', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $phone = self::normalisePhone($customer->phone ?: $customer->whatsapp);
        if ($phone !== '' && in_array($channel, ['sms', 'both'], true)) {
            try {
                app(\App\Http\Controllers\SMSController::class)->sendSms(
                    $phone,
                    "NGN Motors password reset link:\n{$resetUrl}\nThis link expires in 60 minutes."
                );
            } catch (\Throwable $e) {
                Log::warning('Failed to send portal password reset SMS', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return true;
    }
}
