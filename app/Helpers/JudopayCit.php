<?php

namespace App\Helpers;

use App\Http\Requests\JudopayCitPaymentSessionRequest;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;
use App\Models\JudopaySubscription;
use App\Services\JudopayService;
use Carbon\Carbon;
use App\Helpers\JudopayLogSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JudopayCit
{
    public static function initialiseCitSession(Request $request)
    {
        // 1. Validate Request Payload
        $validator = Validator::make($request->all(), (new JudopayCitPaymentSessionRequest)->rules());
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
            ], 422);
        }

        // 1a. Check if subscription exists
        $subscriptionId = $request->input('subscription_id');

        $subscription = JudopaySubscription::find($subscriptionId);

        $subscription->exists() || abort(404);

        // 1b. Check if there's already an active CIT session for this subscription
        // Skip this check for consent form submissions (they should always create new JudoPay links)
        $source = $request->input('source');
        if ($source !== 'consent_form') {
            $existingSession = JudopayCitPaymentSession::where('subscription_id', $request->input('subscription_id'))
                ->where('is_active', true)
                ->whereIn('status', ['created'])
                ->first();
            if ($existingSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Active CIT session already exists for this subscription',
                    'data' => [
                        'existing_session_id' => $existingSession->id,
                        'payment_reference' => $existingSession->judopay_payment_reference,
                        'paylink_url' => $existingSession->judopay_paylink_url,
                    ],
                ], 409); // 409 Conflict
            }
        }

        // 2. Prepare JudoPay CIT Session Payload
        $citSession = JudopayCit::insertCitSession($request);

        // 3. Merge the NGN session ID into the request
        $request->merge(['ngn_session_id' => $citSession->id]);

        // 4. Insert the CIT session into the database
        $payload = JudopayPayload::prepareCitPayload($request);

        // Log the prepared payload
        Log::channel('judopay')->info('JudoPay CIT Session Payload Prepared', [
            'subscription_id' => $request->input('subscription_id'),
            'payment_reference' => $request->input('judopay_payment_reference'),
            'amount' => $request->input('amount'),
            'payload' => JudopayLogSanitizer::sanitizeResponse($payload),
        ]);

        // 5. Make API call to JudoPay
        $response = JudopayCit::invokeCit($payload);

        // 5a. If failed to create CIT session, update the CIT session and return error
        if (! $response->successful()) {
            JudopayCitPaymentSession::where('id', $citSession->id)->update([
                'status' => 'error',
                'failure_reason' => 'Failed to create CIT session',
                'is_active' => false,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create CIT session',
                'data' => [
                    'payload' => $payload,
                    'subscription_id' => $request->input('subscription_id'),
                    'payment_reference' => $request->input('judopay_payment_reference'),
                ],
            ], 500);
        }

        // 5b. If successful, update the CIT session with the response
        JudopayCit::updateCitSession($citSession, $response->json());

        // Refresh the session to get the updated paylink URL
        $citSession->refresh();

        return response()->json([
            'success' => true,
            'message' => 'CIT session created successfully',
            'data' => [
                'payload' => $payload,
                'subscription_id' => $request->input('subscription_id'),
                'payment_reference' => $request->input('judopay_payment_reference'),
                'paylink_url' => $citSession->judopay_paylink_url,
                'order_reference' => $request->input('order_reference'),
                'description' => $request->input('description'),
            ],
        ]);
    }

    public static function insertCitSession(Request $request): JudopayCitPaymentSession
    {
        // Always create fresh session with new payment reference
        return JudopayCitPaymentSession::create([
            'subscription_id' => $request->input('subscription_id'),
            'judopay_payment_reference' => $request->input('judopay_payment_reference'),
            'amount' => $request->input('amount'),
            'customer_email' => $request->input('customer_email'),
            'customer_mobile' => $request->input('customer_mobile'),
            'customer_name' => $request->input('customer_name'),
            'card_holder_name' => $request->input('card_holder_name'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'postcode' => $request->input('postcode'),
            'judopay_reference' => $request->input('judopay_reference'),
            'judopay_receipt_id' => $request->input('judopay_receipt_id'),
            'judopay_paylink_url' => $request->input('judopay_paylink_url'),
            'expiry_date' => now()->addHours(24),
            'status' => $request->input('status', 'created'),
            'is_active' => $request->input('is_active', true),
            'judopay_response' => $request->input('judopay_response'),
            'judopay_webhook_data' => $request->input('judopay_webhook_data'),
            'judopay_session_status' => $request->input('judopay_session_status'),
            'payment_completed_at' => $request->input('payment_completed_at'),
            'link_generated_at' => $request->input('link_generated_at'),
            'customer_accessed_at' => $request->input('customer_accessed_at'),
            'failure_reason' => $request->input('failure_reason'),
            // Consent tracking fields
            'consent_given_at' => $request->input('consent_given_at'),
            'consent_ip_address' => $request->input('consent_ip_address'),
            'consent_terms_version' => $request->input('consent_terms_version'),
            'sms_verification_sid' => $request->input('sms_verification_sid'),
            'sms_verified_at' => $request->input('sms_verified_at'),
            'consent_content_sha256' => $request->input('consent_content_sha256'),
        ]);
    }

    public static function updateCitSession(JudopayCitPaymentSession $citSession, array $responseData): void
    {
        $citSession->update([
            'judopay_reference' => $responseData['reference'],
            'judopay_paylink_url' => $responseData['payByLinkUrl'],
            'link_generated_at' => now(),
            'judopay_response' => $responseData,
        ]);
    }

    public static function invokeCit(array $payload): \Illuminate\Http\Client\Response
    {
        $url = JudopayService::getApiUrl(config('judopay.endpoints.webpayments'));
        $headers = JudopayService::getHeaders();

        Log::channel('judopay')->info('Making JudoPay CIT API call', [
            'url' => $url,
            'headers' => JudopayLogSanitizer::sanitizeHeaders($headers),
            'payload' => JudopayLogSanitizer::sanitizeResponse($payload),
        ]);

        $response = Http::withHeaders($headers)->post($url, $payload);

        // $responseBody = $response->json();

        Log::channel('judopay')->info('JudoPay CIT API response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => JudopayLogSanitizer::sanitizeResponse($response->json()),
            'headers' => JudopayLogSanitizer::sanitizeHeaders($response->headers()),
        ]);

        return $response;
    }

    /**
     * Update JudopayOnboarding status to true when subscription becomes active with card token
     */
    public static function updateOnboardingStatus(JudopaySubscription $subscription): void
    {
        try {
            $onboarding = $subscription->judopayOnboarding;
            if ($onboarding && ! $onboarding->is_onboarded) {
                $onboarding->update(['is_onboarded' => true]);

                Log::channel('judopay')->info('Onboarding status updated to TRUE', [
                    'onboarding_id' => $onboarding->id,
                    'subscription_id' => $subscription->id,
                    'customer_id' => $onboarding->onboardable_id,
                    'customer_type' => $onboarding->onboardable_type,
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to update onboarding status', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private static function inactiveCitSessionsExcept(JudopayCitPaymentSession $citSession): void
    {
        JudopayCitPaymentSession::where('subscription_id', $citSession->subscription_id)
            ->where('id', '!=', $citSession->id)
            ->update(['is_active' => false]);
    }

    /*
    * Used by:
    * JudopayController.webhook()
    */
    public static function processCitWebhook(JudopayCitPaymentSession $citSession, $receipt, string $status): void
    {
        if ($status === 'success') {
            // SUCCESS: Activate subscription and store payment details
            $citSession->status_score += 1;
            $citSession->status = 'success';
            $citSession->judopay_receipt_id = data_get($receipt, 'receiptId');
            $citSession->judopay_webhook_data = $receipt;
            $citSession->card_token = data_get($receipt, 'cardDetails.cardToken');
            $citSession->payment_completed_at = Carbon::parse(data_get($receipt, 'createdAt'));

            // Map subscription fields using helper
            $citSession->subscription->fill(
                JudopayPayload::mapSubscriptionFields($receipt)
            );
            $citSession->subscription->receipt_id = data_get($receipt, 'receiptId');
            $citSession->subscription->status = 'active';
            $citSession->subscription->card_token = data_get($receipt, 'cardDetails.cardToken');
            $citSession->subscription->save();

            // Update onboarding status to true (customer is now onboarded with card token)
            self::updateOnboardingStatus($citSession->subscription);

            // Check if automatic refund should be triggered
            $refundMode = config('judopay.cit.refund_mode', 'manual');
            if ($refundMode === 'automatic') {
                $receiptId = data_get($receipt, 'receiptId');
                $amount = (float) $citSession->amount;
                $currency = config('judopay.currency', 'GBP');
                $paymentReference = JudopayService::generatePaymentReference('refund', $citSession->subscription->consumer_reference ?? "SUB-{$citSession->subscription_id}");

                if ($receiptId && $amount > 0) {
                    // Process refund immediately (synchronously) when automatic mode is enabled
                    \App\Jobs\JudopayRefundJob::processRefund(
                        $receiptId,
                        $amount,
                        $currency,
                        $paymentReference,
                        $citSession->id,
                        $citSession->subscription_id
                    );

                    Log::channel('judopay')->info('Automatic refund processed immediately', [
                        'session_id' => $citSession->id,
                        'receipt_id' => $receiptId,
                        'amount' => $amount,
                        'payment_reference' => $paymentReference,
                    ]);
                }
            }

            self::inactiveCitSessionsExcept($citSession);

            Log::channel('judopay')->info('Payment SUCCESS - Subscription activated and onboarding updated', [
                'subscription_id' => $citSession->subscription->id,
                'onboarding_id' => $citSession->subscription->judopayOnboarding->id ?? 'N/A',
            ]);

        } elseif ($status === 'declined') {
            // DECLINED: Update CIT session but keep subscription PENDING for future attempts
            $citSession->status = 'declined';
            $citSession->judopay_webhook_data = $receipt;
            $citSession->failure_reason = data_get($receipt, 'message', 'Card declined');
            $citSession->is_active = false; // Mark this CIT session as inactive

            // IMPORTANT: Keep subscription status as 'pending' - DO NOT change to declined
            // This allows for future CIT attempts with same or different cards
            // $citSession->subscription->status remains 'pending'

            // Store declined payment attempt details for analysis (non-PCI compliant)
            // This helps identify patterns: card type, country, risk factors, etc.
            $declinedAttemptData = [
                'judopay_receipt_id' => data_get($receipt, 'receiptId'),
                'acquirer_transaction_id' => data_get($receipt, 'acquirerTransactionId'),
                'merchant_name' => data_get($receipt, 'merchantName'),
                'declined_card_last_four' => data_get($receipt, 'cardDetails.cardLastfour'),
                'declined_card_funding' => data_get($receipt, 'cardDetails.cardFunding'),
                'declined_card_category' => data_get($receipt, 'cardDetails.cardCategory'),
                'declined_card_country' => data_get($receipt, 'cardDetails.cardCountry'),
                'declined_issuing_bank' => data_get($receipt, 'cardDetails.bank'),
                'decline_reason' => data_get($receipt, 'message'),
                'risk_assessment' => data_get($receipt, 'risks'),
                'billing_address' => data_get($receipt, 'billingAddress'),
                'three_d_secure' => data_get($receipt, 'threeDSecure'),
            ];

            Log::channel('judopay')->info('Payment DECLINED - Subscription remains pending for future attempts', [
                'subscription_id' => $citSession->subscription->id,
                'decline_reason' => data_get($receipt, 'message'),
                'card_last_four' => data_get($receipt, 'cardDetails.cardLastfour'),
            ]);

        }

        // Update audit log for both success and declined cases
        $existingAuditLog = $citSession->subscription->audit_log;

        if (is_string($existingAuditLog)) {
            $existingAuditLog = json_decode($existingAuditLog, true) ?? [];
        }
        if (! is_array($existingAuditLog)) {
            $existingAuditLog = [];
        }

        // Ensure we have webhook_entries array for storing webhook data
        if (! isset($existingAuditLog['webhook_entries'])) {
            $existingAuditLog['webhook_entries'] = [];
        }

        // Add webhook entry with outcome details
        $webhookEntry = [
            'webhook_processed_at' => now()->toIsoString(),
            'receipt_id' => data_get($receipt, 'receiptId'),
            'payment_status' => $status,
            'yourPaymentMetaData' => data_get($receipt, 'yourPaymentMetaData'),
        ];

        // Add specific data based on outcome
        if ($status === 'success') {
            $webhookEntry['card_token_obtained'] = data_get($receipt, 'cardDetails.cardToken') ? JudopayLogSanitizer::partialRedact(data_get($receipt, 'cardDetails.cardToken'), 2, 2) : null;
            $webhookEntry['auth_code'] = '***REDACTED***';
        } elseif ($status === 'declined') {
            $webhookEntry['decline_reason'] = data_get($receipt, 'message');
            $webhookEntry['attempted_card_last_four'] = data_get($receipt, 'cardDetails.cardLastfour');
            $webhookEntry['attempted_card_country'] = data_get($receipt, 'cardDetails.cardCountry');
        }

        $existingAuditLog['webhook_entries'][] = $webhookEntry;
        $citSession->subscription->audit_log = $existingAuditLog;
        $citSession->subscription->save();
        $citSession->save();

        $metadata = data_get($receipt, 'yourPaymentMetaData', []);
        $sessionId = data_get($metadata, 'ngn_session_id');
        $subscriptionId = data_get($metadata, 'subscription_id');

        // Map outcome using helper
        $insertData = JudopayPayload::mapOutcomeFields(
            $receipt,
            $status,
            'webhook',
            'App\Models\JudopayCitPaymentSession',
            $sessionId,
            $subscriptionId
        );

        // Sanitize the payload field specifically before logging
        $sanitizedInsertData = $insertData;
        if (isset($sanitizedInsertData['payload'])) {
            $sanitizedInsertData['payload'] = JudopayLogSanitizer::sanitizeResponse($sanitizedInsertData['payload']);
        }

        Log::channel('judopay')->info('Insert CIT webhook data', $sanitizedInsertData);

        $outcome = JudopayPaymentSessionOutcome::create($insertData);
        Log::channel('judopay')->info('CIT webhook data inserted successfully with ID: '.$outcome->id);

        // Send notifications based on payment status
        if ($status === 'success') {
            JudopayNotificationHelper::sendCitSuccessNotifications($citSession, $outcome);
        } elseif ($status === 'declined') {
            $failureReason = JudopayNotificationHelper::getFailureReason($outcome, $citSession->status);
            JudopayNotificationHelper::sendCitFailureNotifications($citSession, $outcome, $failureReason);
        }
    }
}
