<?php

namespace App\Helpers;

use App\Models\JudopayMitPaymentSession;
use App\Models\JudopaySubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JudopayPayload
{
    public static function prepareCitPayload(Request $request): array
    {
        return [
            'judoId' => config('judopay.judo_id'),
            'yourConsumerReference' => $request->input('consumer_reference'),
            'yourPaymentReference' => $request->input('judopay_payment_reference'),
            'yourPaymentMetaData' => [
                'ngn_session_id' => $request->input('ngn_session_id'),
                'ngn_session_type' => 'cit',
                'subscription_id' => $request->input('subscription_id'),
                'order_reference' => $request->input('order_reference', 'CIT-'.$request->input('subscription_id')),
                'description' => $request->input('description', 'Cardholder Initiated Transaction'),
                'hideBackButton' => true,
            ],
            'currency' => config('judopay.currency', 'GBP'),
            'amount' => (float) $request->input('amount'),
            'cardAddress' => [
                'address1' => $request->input('address1'),
                'address2' => $request->input('address2'),
                'town' => $request->input('city'),
                'postCode' => $request->input('postcode'),
                'countryCode' => config('judopay.country_code', 826),
                'cardHolderName' => $request->input('card_holder_name'),
            ],
            'expiryDate' => now()->addHours(24)->toIsoString(),
            'isPayByLink' => false,
            'isJudoAccept' => false,
            'successUrl' => config('app.env') === 'local'
                ? 'https://shariq.agentifyops.com/judopay/success/LnPqbMwqAXvCU'
                : config('judopay.cit.success_url', route('cit-payment.success')),
            'cancelUrl' => config('app.env') === 'local'
                ? 'https://shariq.agentifyops.com/judopay/failure/enpWqTqAU'
                : config('judopay.cit.cancel_url', route('cit-payment.failure')),
            'emailAddress' => $request->input('customer_email'),
            'mobileNumber' => $request->input('customer_mobile'),
            'phoneCountryCode' => $request->input('phone_country_code', '44'),
            'threeDSecure' => [
                'challengeRequestIndicator' => $request->input('three_d_secure_challenge', 'challengeAsMandate'),
            ],
            'hideBillingInfo' => false,
            'hideReviewInfo' => false,
        ];
    }

    /**
     * Extract VRM from subscription's subscribable relationship
     * Handles both RentingBooking and FinanceApplication
     * Loads necessary relationships conditionally to avoid eager loading conflicts
     */
    public static function extractVrmFromSubscription(JudopaySubscription $subscription): string
    {
        $subscribable = $subscription->subscribable;
        
        if (!$subscribable) {
            return 'N/A';
        }

        if ($subscribable instanceof \App\Models\RentingBooking) {
            // Load rentingBookingItems with motorbike if not already loaded
            if (!$subscribable->relationLoaded('rentingBookingItems')) {
                $subscribable->load('rentingBookingItems.motorbike');
            }
            return optional(optional($subscribable->rentingBookingItems->first())->motorbike)->reg_no ?? 'N/A';
        }
        
        if ($subscribable instanceof \App\Models\FinanceApplication) {
            // Load application_items with motorbike if not already loaded
            if (!$subscribable->relationLoaded('application_items')) {
                $subscribable->load('application_items.motorbike');
            }
            return optional(optional($subscribable->application_items->first())->motorbike)->reg_no ?? 'N/A';
        }

        return 'N/A';
    }

    /**
     * Prepare MIT payment payload with fail-safe metadata
     * All optional parameters default to null and are only included if provided
     * This ensures the MIT procedure never breaks due to missing metadata
     * 
     * @param JudopaySubscription $subscription
     * @param JudopayMitPaymentSession $mitSession
     * @param string|null $invoiceNumber Invoice number from NGN MIT Queue (optional)
     * @param string|null $vrm Vehicle registration mark (optional)
     * @param int|null $contractId Contract ID (RentingBooking or FinanceApplication ID)
     * @param string|null $contractType Contract type ('RentingBooking' or 'FinanceApplication')
     * @param int|null $ngnMitQueueId NGN MIT Queue ID (for webhook compatibility)
     * @return array MIT payment payload
     */
    public static function prepareMitPayload(
        JudopaySubscription $subscription, 
        JudopayMitPaymentSession $mitSession,
        ?string $invoiceNumber = null,
        ?string $vrm = null,
        ?int $contractId = null,
        ?string $contractType = null,
        ?int $ngnMitQueueId = null
    ): array
    {
        // Base metadata (required)
        $metadata = [
            'ngn_session_id' => $mitSession->id,
            'ngn_session_type' => 'mit',
            'subscription_id' => $subscription->id,
            'order_reference' => $mitSession->order_reference,
            'description' => $mitSession->description,
        ];
        
        // Add ngn_mit_queue_id for webhook compatibility (if provided)
        if ($ngnMitQueueId !== null) {
            $metadata['ngn_mit_queue_id'] = $ngnMitQueueId;
        }

        // Add fail-safe metadata (always include for debugging, even if 'N/A')
        // This ensures we can track what data is available in Judopay webhooks
        if ($invoiceNumber !== null && $invoiceNumber !== '') {
            $metadata['invoice_number'] = $invoiceNumber;
        }
        // Always include VRM (even if 'N/A') for fail-safe tracking
        if ($vrm !== null) {
            $metadata['vrm'] = $vrm;
        }
        // Always include contract_id if available
        if ($contractId !== null) {
            $metadata['contract_id'] = $contractId;
        }
        // Always include contract_type (even if 'N/A') for fail-safe tracking
        if ($contractType !== null && $contractType !== '') {
            $metadata['contract_type'] = $contractType;
        }
        
        // Log metadata for debugging
        \Log::channel('judopay')->debug('MIT payload metadata prepared', [
            'invoice_number' => $invoiceNumber,
            'vrm' => $vrm,
            'contract_id' => $contractId,
            'contract_type' => $contractType,
            'ngn_mit_queue_id' => $ngnMitQueueId,
            'final_metadata' => $metadata,
        ]);

        return [
            'judoId' => config('judopay.judo_id'),
            'yourConsumerReference' => $subscription->consumer_reference,
            'yourPaymentReference' => $mitSession->judopay_payment_reference,
            'amount' => (float) $mitSession->amount,
            'currency' => config('judopay.currency', 'GBP'),
            'cardToken' => $subscription->card_token,
            'recurringPayment' => true,
            'recurringPaymentType' => 'mit',
            'relatedReceiptId' => $subscription->judopay_receipt_id,
            'yourPaymentMetaData' => $metadata,
        ];
    }

    public static function normalize(array $payload): array
    {
        return isset($payload['receipt']) && is_array($payload['receipt'])
            ? $payload['receipt']
            : $payload;
    }

    public static function mapSubscriptionFields(array $receipt): array
    {
        return [
            'judopay_receipt_id' => data_get($receipt, 'receiptId'),
            'acquirer_transaction_id' => data_get($receipt, 'acquirerTransactionId'),
            'auth_code' => data_get($receipt, 'authCode'),
            'merchant_name' => data_get($receipt, 'merchantName'),
            'statement_descriptor' => data_get($receipt, 'appearsOnStatementAs'),

            // Card (safe, non-PCI)
            'card_token' => data_get($receipt, 'cardDetails.cardToken'),
            'card_last_four' => data_get($receipt, 'cardDetails.cardLastfour'),
            'card_funding' => data_get($receipt, 'cardDetails.cardFunding'),
            'card_category' => data_get($receipt, 'cardDetails.cardCategory'),
            'card_country' => data_get($receipt, 'cardDetails.cardCountry'),
            'issuing_bank' => data_get($receipt, 'cardDetails.bank'),

            // Compliance / security
            'billing_address' => data_get($receipt, 'billingAddress'),
            'risk_assessment' => data_get($receipt, 'risks'),
            'three_d_secure' => data_get($receipt, 'threeDSecure'),
        ];
    }

    public static function mapOutcomeFields(array $receipt, string $status, string $source, string $sessionType, int|string $sessionId, int|string $subscriptionId): array
    {
        return [
            'session_id' => $sessionId,
            'session_type' => $sessionType,
            'subscription_id' => $subscriptionId,
            'status' => $status,
            'source' => $source,
            'judopay_receipt_id' => data_get($receipt, 'receiptId'),
            'amount' => data_get($receipt, 'amount'),
            'your_payment_reference' => data_get($receipt, 'yourPaymentReference'),
            'your_consumer_reference' => data_get($receipt, 'consumer.yourConsumerReference'),
            'payload' => $receipt,
            'message' => data_get($receipt, 'message'),
            'occurred_at' => now(),

            // Additional
            'payment_network_transaction_id' => data_get($receipt, 'paymentNetworkTransactionId'),
            'acquirer_transaction_id' => data_get($receipt, 'acquirerTransactionId'),
            'auth_code' => data_get($receipt, 'authCode'),
            'external_bank_response_code' => data_get($receipt, 'externalBankResponseCode'),
            'appears_on_statement_as' => data_get($receipt, 'appearsOnStatementAs'),
            'type' => data_get($receipt, 'type'),

            // Card (safe, non-PCI)
            'card_last_four' => data_get($receipt, 'cardDetails.cardLastfour'),
            'card_funding' => data_get($receipt, 'cardDetails.cardFunding'),
            'card_category' => data_get($receipt, 'cardDetails.cardCategory'),
            'card_country' => data_get($receipt, 'cardDetails.cardCountry'),
            'issuing_bank' => data_get($receipt, 'cardDetails.bank'),

            // Extra blobs
            'billing_address' => data_get($receipt, 'billingAddress'),
            'risk_assessment' => data_get($receipt, 'risks'),
            'three_d_secure' => data_get($receipt, 'threeDSecure'),

            // NEW COMPLIANCE FIELDS - Using actual JudoPay data
            'merchant_name' => data_get($receipt, 'merchantName'),
            'judo_id' => data_get($receipt, 'judoId'),
            'net_amount' => data_get($receipt, 'netAmount'),
            'original_amount' => data_get($receipt, 'originalAmount'),
            'amount_collected' => data_get($receipt, 'amountCollected'),

            // Enhanced transaction tracking
            'locator_id' => data_get($receipt, 'locatorId'), // May not always be present
            'disable_network_tokenisation' => data_get($receipt, 'disableNetworkTokenisation'),
            'allow_increment' => data_get($receipt, 'allowIncrement'),

            // Risk and compliance
            'risk_score' => data_get($receipt, 'riskScore'),
            'recurring_payment_type' => data_get($receipt, 'recurringPaymentType'), // CIT/MIT

            // Bank response classification
            'bank_response_category' => self::getBankResponseCategory(data_get($receipt, 'externalBankResponseCode')),
            'is_retryable' => self::isRetryableResponse(data_get($receipt, 'externalBankResponseCode')),

            // Compliance timestamps
            'judopay_created_at' => data_get($receipt, 'createdAt') ? Carbon::parse(data_get($receipt, 'createdAt')) : null,
            'timezone' => self::extractTimezone(data_get($receipt, 'createdAt')),
        ];
    }

    public static function getBankResponseCategory(?string $code): ?string
    {
        Log::channel('judopay')->info('getBankResponseCategory Bank Response Code: '.$code);

        return config('judopay.bank_response_codes')[$code ?? ''] ?? 'UNKNOWN';
    }

    public static function isRetryableResponse(?string $code): bool
    {
        Log::channel('judopay')->info('isRetryableResponse Bank Response Code: '.$code);

        // Response Reference: config('judopay.bank_response_codes')
        return in_array($code ?? '', ['05', '5', '09', '9', '19', '28', '80']);
    }

    public static function extractTimezone(?string $dateString): ?string
    {
        if (! $dateString) {
            return null;
        }

        // Extract timezone from ISO string like "2025-09-27T20:12:03.5053+01:00"
        if (preg_match('/([+-]\d{2}:\d{2}|Z)$/', $dateString, $matches)) {
            return $matches[1] === 'Z' ? 'UTC' : $matches[1];
        }

        return null;
    }
}
