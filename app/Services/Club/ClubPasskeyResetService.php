<?php

namespace App\Services\Club;

use App\Http\Controllers\SMSController;
use App\Models\ClubMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClubPasskeyResetService
{
    public function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/^\+44/', '0', trim($phone));

        return preg_replace('/\s+/', '', $phone) ?? '';
    }

    public function findMemberByIdentifier(string $raw): ?ClubMember
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        if (str_contains($raw, '@')) {
            $email = strtolower($raw);
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return null;
            }

            return ClubMember::query()->whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        }

        $phone = $this->normalisePhone($raw);
        if ($phone === '') {
            return null;
        }

        return ClubMember::query()->where('phone', $phone)->first();
    }

    /**
     * @return array{success: bool, message: string, channel?: string}
     */
    public function sendVerificationCodeForIdentifier(string $identifier): array
    {
        $member = $this->findMemberByIdentifier($identifier);
        if (! $member) {
            if (str_contains(trim($identifier), '@')) {
                return ['success' => false, 'message' => 'This email address is not registered with NGN Club.'];
            }

            return ['success' => false, 'message' => 'This phone number is not registered with NGN Club.'];
        }

        $phone = $this->normalisePhone((string) $member->phone);
        if ($phone === '') {
            return ['success' => false, 'message' => 'We could not verify your membership contact details. Please contact support.'];
        }

        $verificationCode = (string) random_int(100000, 999999);

        $codeHash = Hash::make($verificationCode);
        $expiresAt = Carbon::now()->addMinutes(10);

        session([
            'verification_code' => $codeHash,
            'verification_code_expires_at' => $expiresAt,
            'verification_phone' => $phone,
            'forgot_delivery_channel' => str_contains(trim($identifier), '@') ? 'email' : 'sms',
        ]);

        $resetToken = (string) Str::uuid();
        Cache::put($this->cacheKeyForToken($resetToken), [
            'code_hash' => $codeHash,
            'expires_at' => $expiresAt->timestamp,
            'phone' => $phone,
        ], $expiresAt->copy()->addMinute());

        $usedEmail = str_contains(trim($identifier), '@');
        $continueUrlShort = url('/ngn-club/forgot?t='.urlencode($resetToken));
        $continueUrlWithCode = $continueUrlShort.'&verification_code='.urlencode($verificationCode);

        try {
            if ($usedEmail) {
                $email = strtolower(trim((string) $member->email));
                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone', 'forgot_delivery_channel']);
                    Cache::forget($this->cacheKeyForToken($resetToken));

                    return ['success' => false, 'message' => 'No valid email is on file for this account. Please use your registered phone number instead.'];
                }

                Mail::raw(
                    "Your NGN Club passkey reset code is: {$verificationCode}\n\n"
                    ."Open this link to go straight to the reset step (works on any device). The code is filled in for you:\n{$continueUrlWithCode}\n\n"
                    ."Or open: {$continueUrlShort} and type the code yourself.\n\n"
                    ."This code expires in 10 minutes.\n\nIf you did not request this, please ignore this email.",
                    function ($message) use ($email) {
                        $message->to($email)->subject('NGN Club – passkey reset code');
                    }
                );

                return [
                    'success' => true,
                    'message' => 'We have sent a verification code to your email address.',
                    'channel' => 'email',
                ];
            }

            $smsController = new SMSController;
            $smsBody = 'NGN Club reset code: '.$verificationCode.'. Open: '.$continueUrlShort;
            $smsResponse = $smsController->sendSms($phone, $smsBody);

            if (isset($smsResponse['success']) && $smsResponse['success']) {
                return [
                    'success' => true,
                    'message' => 'We have sent a verification code to your phone by SMS.',
                    'channel' => 'sms',
                ];
            }

            session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone', 'forgot_delivery_channel']);
            Cache::forget($this->cacheKeyForToken($resetToken));

            return [
                'success' => false,
                'message' => $smsResponse['message'] ?? 'Failed to send verification code. Please try again.',
            ];
        } catch (\Throwable $e) {
            Log::error('Club passkey reset send code failed: '.$e->getMessage());
            session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone', 'forgot_delivery_channel']);
            Cache::forget($this->cacheKeyForToken($resetToken));

            return ['success' => false, 'message' => 'An error occurred while sending the verification code. Please try again.'];
        }
    }

    public function cacheKeyForToken(string $token): string
    {
        return 'club_passkey_reset:'.$token;
    }

    /**
     * @return array{phone: string}|null
     */
    public function peekResetToken(string $token): ?array
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        $payload = Cache::get($this->cacheKeyForToken($token));
        if (! is_array($payload) || empty($payload['phone']) || empty($payload['expires_at'])) {
            return null;
        }

        if (now()->timestamp > (int) $payload['expires_at']) {
            Cache::forget($this->cacheKeyForToken($token));

            return null;
        }

        return ['phone' => (string) $payload['phone']];
    }

    /**
     * @return array{success: bool, message: string, phone?: string}
     */
    public function resetPasskeyWithCode(string $plainVerificationCode, ?string $submittedPhoneForLegacyForm = null, ?string $cacheToken = null): array
    {
        $plainVerificationCode = preg_replace('/\D/', '', $plainVerificationCode);

        if (strlen($plainVerificationCode) !== 6) {
            return ['success' => false, 'message' => 'Enter the 6-digit verification code.'];
        }

        $phone = null;
        $codeHash = null;
        $usedCacheToken = null;

        $cacheToken = $cacheToken !== null ? trim($cacheToken) : '';
        if ($cacheToken !== '') {
            $payload = Cache::get($this->cacheKeyForToken($cacheToken));
            if (! is_array($payload) || empty($payload['phone']) || empty($payload['code_hash']) || empty($payload['expires_at'])) {
                return ['success' => false, 'message' => 'This reset link is invalid or has expired. Please request a new code.'];
            }
            if (now()->timestamp > (int) $payload['expires_at']) {
                Cache::forget($this->cacheKeyForToken($cacheToken));

                return ['success' => false, 'message' => 'This reset link has expired. Please request a new code.'];
            }
            $phone = (string) $payload['phone'];
            $codeHash = (string) $payload['code_hash'];
            $usedCacheToken = $cacheToken;
        } elseif (session()->has('verification_code') && session()->has('verification_code_expires_at') && session()->has('verification_phone')) {
            if ($submittedPhoneForLegacyForm !== null) {
                $submitted = $this->normalisePhone($submittedPhoneForLegacyForm);
                if ($submitted === '' || $submitted !== session('verification_phone')) {
                    return ['success' => false, 'message' => 'Phone number does not match the one used to receive the verification code.'];
                }
            }

            if (Carbon::now()->greaterThan(session('verification_code_expires_at'))) {
                session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone', 'forgot_delivery_channel']);

                return ['success' => false, 'message' => 'Verification code has expired. Please request a new code.'];
            }

            $phone = session('verification_phone');
            $codeHash = session('verification_code');
        } else {
            return ['success' => false, 'message' => 'Verification code not found. Please request a new code or open the link from your message or email.'];
        }

        if (! Hash::check($plainVerificationCode, $codeHash)) {
            return ['success' => false, 'message' => 'Invalid verification code. Please try again.'];
        }

        session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone', 'forgot_delivery_channel']);
        if ($usedCacheToken) {
            Cache::forget($this->cacheKeyForToken($usedCacheToken));
        }

        $newPasskey = random_int(100000, 999999);

        $clubMember = ClubMember::where('phone', $phone)->first();
        if (! $clubMember) {
            return ['success' => false, 'message' => 'Member not found.'];
        }

        $clubMember->update(['passkey' => $newPasskey]);

        $phoneOut = $phone;
        $passkeyOut = $newPasskey;
        $emailOut = strtolower(trim((string) $clubMember->email));

        app()->terminating(function () use ($phoneOut, $passkeyOut, $emailOut) {
            try {
                $smsController = new SMSController;
                $smsMessage = "Your NGN Club Login Details:\n\n"
                    .'Phone: '.$phoneOut."\n"
                    .'Password: '.$passkeyOut."\n\n"
                    .'Login Link: '.url('/ngn-club/login?phone='.urlencode($phoneOut));

                $smsController->sendSms($phoneOut, $smsMessage);

                if ($emailOut !== '' && filter_var($emailOut, FILTER_VALIDATE_EMAIL)) {
                    Mail::raw(
                        "Your NGN Club passkey has been reset.\n\nPhone: {$phoneOut}\nNew passkey: {$passkeyOut}\n\nLog in at: ".url('/ngn-club/login'),
                        function ($message) use ($emailOut) {
                            $message->to($emailOut)->subject('NGN Club – new passkey');
                        }
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Club passkey reset notification (after response): '.$e->getMessage());
            }
        });

        return [
            'success' => true,
            'message' => 'Your passkey has been reset. Check your phone and email for your new login details — they may take a minute to arrive.',
            'phone' => $phone,
        ];
    }
}
