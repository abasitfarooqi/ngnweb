<?php

namespace App\Helpers;

use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;
use App\Models\User;
use App\Notifications\CitSuccessInternalNotification;
use App\Notifications\CitFailureInternalNotification;
use App\Notifications\CitSuccessCustomerNotification;
use App\Notifications\CitFailureCustomerNotification;
use App\Notifications\CitSuccessCustomerServiceNotification;
use App\Notifications\CitRefundInternalNotification;
use App\Notifications\CitRefundCustomerNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class JudopayNotificationHelper
{
    /**
     * Send notifications for successful CIT payment
     */
    public static function sendCitSuccessNotifications(JudopayCitPaymentSession $citSession, JudopayPaymentSessionOutcome $outcome): void
    {
        try {
            $subscription = $citSession->subscription;
            
            // Standardised customer resolution
            $customer = $subscription->subscribable?->customer ?? $subscription->customer;

            $adminUser = User::where('email', 'thiago@neguinhomotors.co.uk')->first();
            
            Log::channel('judopay')->info('Attempting to send CIT success notifications', [
                'cit_session_id' => $citSession->id,
                'subscription_id' => $subscription->id,
                'customer_id' => $customer?->id,
                'admin_user_found' => $adminUser ? true : false,
                'admin_user_id' => $adminUser?->id,
            ]);
            
            if ($adminUser) {
                $adminUser->notify(new CitSuccessInternalNotification($citSession, $outcome));
                Log::channel('judopay')->info('CIT success internal notification sent to admin', [
                    'admin_user_id' => $adminUser->id,
                    'admin_email' => $adminUser->email,
                ]);
            } else {
                Log::channel('judopay')->warning('Admin user not found for CIT success notification', [
                    'email' => 'thiago@neguinhomotors.co.uk',
                ]);
            }

            // Send customer notification
            if ($customer) {
                $customer->notify(new CitSuccessCustomerNotification($citSession, $outcome));
                Log::channel('judopay')->info('CIT success customer notification sent', [
                    'customer_id' => $customer->id,
                    'customer_email' => $customer->email,
                ]);
            } else {
                Log::channel('judopay')->warning('Customer not found for CIT success notification', [
                    'subscription_id' => $subscription->id,
                ]);
            }

            // Send customer service notification
            $customerServiceUser = User::where('email', 'customerservice@neguinhomotors.co.uk')->first();
            
            if ($customerServiceUser) {
                $customerServiceUser->notify(new CitSuccessCustomerServiceNotification($citSession, $outcome));
                Log::channel('judopay')->info('CIT success customer service notification sent', [
                    'subscription_id' => $subscription->id,
                    'customer_id' => $customer?->id,
                    'customer_service_user_id' => $customerServiceUser->id,
                    'customer_service_email' => $customerServiceUser->email,
                ]);
            } else {
                try {
                    Notification::route('mail', 'customerservice@neguinhomotors.co.uk')
                        ->notify(new CitSuccessCustomerServiceNotification($citSession, $outcome));
                    Log::channel('judopay')->info('CIT success customer service notification sent via route', [
                        'subscription_id' => $subscription->id,
                        'customer_id' => $customer?->id,
                    ]);
                } catch (\Exception $mailException) {
                    Log::channel('judopay')->error('Failed to send CIT success customer service notification', [
                        'subscription_id' => $subscription->id,
                        'error' => $mailException->getMessage(),
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to send CIT success notifications', [
                'cit_session_id' => $citSession->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Send notifications for failed CIT payment
     */
    public static function sendCitFailureNotifications(JudopayCitPaymentSession $citSession, ?JudopayPaymentSessionOutcome $outcome = null, string $failureReason = 'Payment declined'): void
    {
        try {
            $subscription = $citSession->subscription;
            
            // Standardised customer resolution
            $customer = $subscription->subscribable?->customer ?? $subscription->customer;

            $adminUser = User::where('email', 'thiago@neguinhomotors.co.uk')->first();
            
            if ($adminUser) {
                $adminUser->notify(new CitFailureInternalNotification($citSession, $outcome, $failureReason));
            }

            // Send customer notification
            if ($customer) {
                $customer->notify(new CitFailureCustomerNotification($citSession, $failureReason));
            }
            
        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to send CIT failure notifications', [
                'cit_session_id' => $citSession->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Determine failure reason from outcome data
     */
    public static function getFailureReason(?JudopayPaymentSessionOutcome $outcome = null, string $sessionStatus = 'declined'): string
    {
        if (!$outcome) {
            return match ($sessionStatus) {
                'declined' => 'Payment was declined by the bank',
                'cancelled' => 'Payment was cancelled by the customer',
                'expired' => 'Payment session expired',
                'failed' => 'Payment failed due to technical issues',
                default => 'Payment authorisation failed',
            };
        }

        // Use webhook data to determine specific failure reason
        $bankResponseCode = $outcome->external_bank_response_code ?? null;
        $riskScore = $outcome->risk_score ?? null;

        if ($bankResponseCode) {
            return match ($bankResponseCode) {
                '05' => 'Payment declined - Do not honor',
                '14' => 'Payment declined - Invalid card number',
                '51' => 'Payment declined - Insufficient funds',
                '54' => 'Payment declined - Expired card',
                '57' => 'Payment declined - Transaction not permitted',
                '61' => 'Payment declined - Exceeds withdrawal limit',
                '62' => 'Payment declined - Restricted card',
                '65' => 'Payment declined - Exceeds withdrawal frequency',
                '75' => 'Payment declined - PIN tries exceeded',
                '78' => 'Payment declined - Invalid PIN',
                '91' => 'Payment declined - Issuer unavailable',
                '96' => 'Payment declined - System malfunction',
                default => 'Payment declined by bank (Code: ' . $bankResponseCode . ')',
            };
        }

        if ($riskScore && $riskScore > 50) {
            return 'Payment declined due to high risk score (' . $riskScore . ')';
        }

        return 'Payment authorisation failed';
    }

    /**
     * Send notifications for CIT refund
     * Critical: Admin (thiago@neguinhomotors.co.uk) - sees critical details not shared with staff/customer
     * Normal: Customer Service (customerservice@neguinhomotors.co.uk) - internal, shows who clicked refund button
     * Normal: Customer - simple email
     */
    public static function sendCitRefundNotifications(
        JudopayCitPaymentSession $citSession,
        JudopayPaymentSessionOutcome $refundOutcome,
        JudopayPaymentSessionOutcome $originalOutcome
    ): void {
        try {
            $subscription = $citSession->subscription;
            
            // Standardised customer resolution
            $customer = $subscription->subscribable?->customer ?? $subscription->customer;

            // Get user who initiated refund for internal notifications
            $refundedByUser = null;
            $payload = $refundOutcome->payload ?? [];
            $refundedByUserId = data_get($payload, 'refunded_by_user_id');
            if ($refundedByUserId) {
                $refundedByUser = User::find($refundedByUserId);
            }

            // CRITICAL: Send to admin (thiago@neguinhomotors.co.uk) - sees critical details
            $adminUser = User::where('email', 'thiago@neguinhomotors.co.uk')->first();
            
            Log::channel('judopay')->info('Attempting to send CIT refund notifications', [
                'cit_session_id' => $citSession->id,
                'subscription_id' => $subscription->id,
                'customer_id' => $customer?->id,
                'admin_user_found' => $adminUser ? true : false,
                'admin_user_id' => $adminUser?->id,
                'refunded_by_user_id' => $refundedByUserId,
            ]);
            
            if ($adminUser) {
                $adminUser->notify(new CitRefundInternalNotification($citSession, $refundOutcome, $originalOutcome, $refundedByUser));
                Log::channel('judopay')->info('CIT refund critical notification sent to admin', [
                    'admin_user_id' => $adminUser->id,
                    'admin_email' => $adminUser->email,
                ]);
            } else {
                Log::channel('judopay')->warning('Admin user not found for CIT refund notification', [
                    'email' => 'thiago@neguinhomotors.co.uk',
                ]);
            }

            // NORMAL: Send customer notification (simple)
            if ($customer) {
                $customer->notify(new CitRefundCustomerNotification($citSession, $refundOutcome, $originalOutcome));
                Log::channel('judopay')->info('CIT refund customer notification sent', [
                    'customer_id' => $customer->id,
                    'customer_email' => $customer->email,
                ]);
            } else {
                Log::channel('judopay')->warning('Customer not found for CIT refund notification', [
                    'subscription_id' => $subscription->id,
                ]);
            }

            // NORMAL: Send customer service notification (internal - shows who clicked refund button)
            $customerServiceUser = User::where('email', 'customerservice@neguinhomotors.co.uk')->first();
            
            if ($customerServiceUser) {
                $customerServiceUser->notify(new CitRefundInternalNotification($citSession, $refundOutcome, $originalOutcome, $refundedByUser));
                Log::channel('judopay')->info('CIT refund customer service notification sent', [
                    'subscription_id' => $subscription->id,
                    'customer_id' => $customer?->id,
                    'customer_service_user_id' => $customerServiceUser->id,
                    'customer_service_email' => $customerServiceUser->email,
                ]);
            } else {
                try {
                    Notification::route('mail', 'customerservice@neguinhomotors.co.uk')
                        ->notify(new CitRefundInternalNotification($citSession, $refundOutcome, $originalOutcome, $refundedByUser));
                    Log::channel('judopay')->info('CIT refund customer service notification sent via route', [
                        'subscription_id' => $subscription->id,
                        'customer_id' => $customer?->id,
                    ]);
                } catch (\Exception $mailException) {
                    Log::channel('judopay')->error('Failed to send CIT refund customer service notification', [
                        'subscription_id' => $subscription->id,
                        'error' => $mailException->getMessage(),
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to send CIT refund notifications', [
                'cit_session_id' => $citSession->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
