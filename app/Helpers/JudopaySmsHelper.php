<?php

namespace App\Helpers;

use App\Http\Controllers\SMSController;
use App\Notifications\JudopayConsentEmailNotification;
use App\Notifications\JudopayConsentSmsNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class JudopaySmsHelper
{
    public static function sendVerificationCode(string $phoneNumber, string $context = 'consent', ?string $customerEmail = null, string $customerName = '', string $serviceType = '', bool $sendEmail = true): array
    {
        try {
            // Generate 6-digit verification code
            $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Create a temporary notifiable object for SMS
            $notifiable = (object) ['phone' => $phoneNumber];

            // Send SMS notification
            $smsNotification = new JudopayConsentSmsNotification($verificationCode, $context);
            $smsResult = $smsNotification->toSms($notifiable);

            // Send SMS using SMSController (since our SmsChannel uses it)
            $smsController = new SMSController;
            $result = $smsController->sendSms($phoneNumber, $smsResult);

            // Send email notification if email provided and sendEmail is true
            if ($customerEmail && $sendEmail) {
                try {
                    Notification::route('mail', $customerEmail)
                        ->notify(new JudopayConsentEmailNotification($verificationCode, $customerName, $serviceType, $context));

                    Log::channel('judopay')->info('Email verification code sent', [
                        'email' => $customerEmail,
                        'context' => $context,
                        'code' => $verificationCode,
                    ]);
                } catch (\Exception $emailError) {
                    Log::channel('judopay')->warning('Failed to send email verification', [
                        'email' => $customerEmail,
                        'error' => $emailError->getMessage(),
                    ]);
                }
            }

            // Log the verification attempt
            Log::channel('judopay')->info('SMS Verification Code Sent', [
                'phone_number' => $phoneNumber,
                'email' => $customerEmail,
                'context' => $context,
                'success' => $result['success'],
                'code' => $verificationCode, // Log actual code for debugging
                'sms_result' => $result['success'] ? 'Sent successfully' : ($result['message'] ?? 'Unknown error'),
            ]);

            if ($result['success']) {
                // Store verification data in session for validation
                session([
                    'sms_verification' => [
                        'code' => $verificationCode,
                        'phone' => $phoneNumber,
                        'sent_at' => now(),
                        'context' => $context,
                        'sid' => $result['sid'] ?? null, // Store SMS SID for audit trail
                    ],
                ]);

                return [
                    'success' => true,
                    'message' => 'Verification code sent successfully',
                    'code' => $verificationCode, // For testing - remove in production
                    'sid' => $result['sid'] ?? null, // Return SMS SID for logging
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: '.($result['message'] ?? 'Unknown error'),
                    'code' => null,
                ];
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('SMS Verification Error', [
                'phone_number' => $phoneNumber,
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'System error: '.$e->getMessage(),
                'code' => null,
            ];
        }
    }

    public static function verifyCode(string $enteredCode, int $validityMinutes = 10): array
    {
        try {
            $verificationData = session('sms_verification');

            if (! $verificationData) {
                return [
                    'valid' => false,
                    'message' => 'No verification code found. Please request a new one.',
                ];
            }

            // Check if code matches
            if (! isset($verificationData['code']) || $enteredCode !== $verificationData['code']) {
                Log::channel('judopay')->info('SMS Verification Failed - Invalid Code', [
                    'entered_code' => $enteredCode,
                    'expected_code' => $verificationData['code'] ?? 'NONE',
                    'phone' => $verificationData['phone'] ?? 'UNKNOWN',
                ]);

                return [
                    'valid' => false,
                    'message' => 'Invalid verification code.',
                ];
            }

            // Check if code is still valid (not expired)
            $sentAt = $verificationData['sent_at'] ?? null;
            if (! $sentAt || now()->diffInMinutes($sentAt) > $validityMinutes) {
                // Clear expired verification data
                session()->forget('sms_verification');

                Log::channel('judopay')->info('SMS Verification Failed - Code Expired', [
                    'phone' => $verificationData['phone'] ?? 'UNKNOWN',
                    'sent_at' => $sentAt,
                    'age_minutes' => $sentAt ? now()->diffInMinutes($sentAt) : 'UNKNOWN',
                ]);

                return [
                    'valid' => false,
                    'message' => 'Verification code expired. Please request a new one.',
                ];
            }

            // Valid code - extract SID before clearing session data
            $smsSid = $verificationData['sid'] ?? null;
            
            // Clear verification data
            session()->forget('sms_verification');

            Log::channel('judopay')->info('SMS Verification Success', [
                'phone' => $verificationData['phone'],
                'context' => $verificationData['context'] ?? 'unknown',
                'verified_at' => now(),
                'sms_sid' => $smsSid,
            ]);

            return [
                'valid' => true,
                'message' => 'Verification successful',
                'sms_sid' => $smsSid,
            ];

        } catch (\Exception $e) {
            Log::channel('judopay')->error('SMS Verification System Error', [
                'error' => $e->getMessage(),
                'entered_code' => $enteredCode,
            ]);

            return [
                'valid' => false,
                'message' => 'System error during verification.',
            ];
        }
    }

    /**
     * Send authorization link via email (no SMS verification code needed)
     */
    public static function sendAuthorizationLink(string $authorizationUrl, string $customerEmail, string $customerName, string $serviceType = '', string $expiresAt = '', ?string $subscriptionId = null, ?string $contractId = null, ?string $vrm = null): array
    {
        try {
            \Log::channel('judopay')->info('Sending authorization link via email', [
                'email' => $customerEmail,
                'customer_name' => $customerName,
                'service_type' => $serviceType,
                'url' => $authorizationUrl,
                'expires_at' => $expiresAt,
            ]);

            // Send email notification with authorization link
            try {
                Notification::route('mail', $customerEmail)
                    ->notify(new JudopayConsentEmailNotification($authorizationUrl, $customerName, $serviceType, $expiresAt, $subscriptionId, $contractId, $vrm));

                \Log::channel('judopay')->info('Authorization email sent successfully', [
                    'email' => $customerEmail,
                    'customer_name' => $customerName,
                    'authorization_url' => $authorizationUrl,
                ]);

                return [
                    'success' => true,
                    'message' => 'Authorization link sent successfully via email',
                    'url' => $authorizationUrl,
                ];

            } catch (\Exception $emailError) {
                \Log::channel('judopay')->error('Failed to send authorization email', [
                    'email' => $customerEmail,
                    'error' => $emailError->getMessage(),
                    'trace' => $emailError->getTraceAsString(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send authorization email: '.$emailError->getMessage(),
                    'url' => null,
                ];
            }

        } catch (\Exception $e) {
            \Log::channel('judopay')->error('Authorization link sending error', [
                'email' => $customerEmail,
                'customer_name' => $customerName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'System error: '.$e->getMessage(),
                'url' => null,
            ];
        }
    }
}
