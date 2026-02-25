<?php

namespace App\Helpers;

use App\Models\JudopaySubscription;
use App\Models\NgnMitQueue;
use App\Services\JudopayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\NgnMitQueueMaker;
use App\Models\JudopayMitQueue;
use App\Models\User;
use App\Models\JudopayMitPaymentSession;
use Carbon\Carbon;

class JudopayMit
{
    /**
     * Generate MIT queue records for the current week
     * Centralized method for MIT queue generation logic
     */
    public static function generateMitQueue(): array
    {
        try {
            Log::channel('judopay')->info('Starting MIT Queue generation via JudopayMit helper');

            // Get invoice data from helper
            $invoices = NgnMitQueueMaker::makeNgnMitQueue();

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($invoices as $invoice) {
                // Find the subscription ID for this invoice
                $subscription = self::findSubscriptionForInvoice($invoice);

                if (!$subscription) {
                    Log::warning("No subscription found for invoice: {$invoice['id']}");
                    continue;
                }

                // Check if this invoice already exists in the queue (regardless of status)
                $invoiceNumber = $invoice['invoice_number'] ?? $invoice['id'];
                $existingQueue = NgnMitQueue::where('subscribable_id', $subscription->id)
                    ->where('invoice_number', $invoiceNumber)
                    ->first();

                if ($existingQueue) {
                    Log::channel('judopay')->info("Skipping duplicate invoice", [
                        'invoice_id' => $invoice['id'],
                        'invoice_number' => $invoiceNumber,
                        'subscription_id' => $subscription->id,
                        'existing_queue_id' => $existingQueue->id,
                        'existing_status' => $existingQueue->status,
                        'existing_attempt' => $existingQueue->mit_attempt,
                        'existing_cleared' => $existingQueue->cleared,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // Create new NGN MIT Queue record using config-driven timing
                self::createMitQueueRecord($subscription, $invoice);
                $createdCount++;

                Log::channel('judopay')->info("Created NGN MIT Queue for invoice: {$invoice['id']} with subscription: {$subscription->id}");
            }

            Log::info("MIT Queue generation completed. Created: {$createdCount}, Skipped: {$skippedCount}");

            return [
                'success' => true,
                'created_count' => $createdCount,
                'skipped_count' => $skippedCount,
                'message' => "MIT Queue generation completed. Created: {$createdCount}, Skipped: {$skippedCount}"
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate MIT Queue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to generate MIT Queue: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Find subscription for invoice based on type
     */
    private static function findSubscriptionForInvoice(array $invoice): ?JudopaySubscription
    {
        if ($invoice['type'] === 'RentingBooking') {
            return JudopaySubscription::where('subscribable_type', 'App\Models\RentingBooking')
                ->where('subscribable_id', $invoice['booking_id'])
                ->first();
        } elseif ($invoice['type'] === 'FinanceApplication') {
            return JudopaySubscription::where('subscribable_type', 'App\Models\FinanceApplication')
                ->where('subscribable_id', $invoice['id'])
                ->first();
        }

        return null;
    }

    /**
     * Create MIT queue record with config-driven fire timing
     */
    private static function createMitQueueRecord(JudopaySubscription $subscription, array $invoice): void
    {
        // Always fire on the invoice due date at the configured exec time
        $invoiceDate = Carbon::createFromFormat('Ymd', $invoice['invoice_date']);

        // Monday invoices fire at 23:45 to avoid race condition (queue produces Monday 08:00)
        if ($invoiceDate->dayOfWeek === Carbon::MONDAY) {
            $mitFireDate = $invoiceDate->copy()->setTime(23, 45, 0);
        } else {
            $execTime = config('judopay.mit.queue_exec_time');
            $mitFireDate = $invoiceDate->copy()->setTimeFromTimeString($execTime);
        }

        // Create new NGN MIT Queue record
        NgnMitQueue::create([
            'subscribable_id' => $subscription->id,
            'invoice_number' => $invoice['invoice_number'] ?? $invoice['id'],
            'invoice_date' => $invoiceDate->format('Y-m-d'),
            'mit_fire_date' => $mitFireDate->format('Y-m-d H:i:s'),
            'mit_attempt' => 'not attempt',
            'status' => 'generated',
            'cleared' => false,
        ]);
    }

    /**
     * Get MIT queue generation schedule configuration
     */
    public static function getQueueGenerationSchedule(): array
    {
        return [
            'time' => config('judopay.mit.queue_produce_time', '07:45'),
            'frequency' => config('judopay.mit.queue_produce_frequency', 'weekly'),
            'description' => 'Produces the NGN MIT Queue using config-driven timing'
        ];
    }

    /**
     * Fire MIT payment directly without using queue tables
     * Creates session record first, then fires MIT immediately for webhook compatibility
     *
     * @param int $subscriptionId The subscription ID to fire MIT for
     * @param int $authorizedByUserId The user ID authorizing this payment
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public static function fireDirectMit(int $subscriptionId, int $authorizedByUserId): array
    {
        try {
            Log::channel('judopay')->info('Starting direct MIT firing', [
                'subscription_id' => $subscriptionId,
                'authorized_by' => $authorizedByUserId,
            ]);

            // Find and validate subscription with necessary relationships
            // Note: subscribable relationships (rentingBookingItems/application_items) are loaded conditionally in extractVrmFromSubscription
            $subscription = JudopaySubscription::with([
                'judopayOnboarding.onboardable',
                'subscribable', // Load subscribable (RentingBooking or FinanceApplication)
            ])->find($subscriptionId);

            if (!$subscription) {
                return [
                    'success' => false,
                    'message' => "Subscription #{$subscriptionId} not found",
                ];
            }

            // Validate subscription is active and has card token
            if ($subscription->status !== 'active') {
                return [
                    'success' => false,
                    'message' => "Subscription #{$subscriptionId} is not active",
                ];
            }

            if (empty($subscription->card_token)) {
                return [
                    'success' => false,
                    'message' => "Subscription #{$subscriptionId} has no card token - CIT setup required",
                ];
            }

            if (empty($subscription->judopay_receipt_id)) {
                return [
                    'success' => false,
                    'message' => "Subscription #{$subscriptionId} has no related receipt ID",
                ];
            }

            // Generate payment reference using existing service
            $consumerReference = $subscription->consumer_reference ?? "SUB-{$subscription->id}";
            $paymentReference = JudopayService::generatePaymentReference('mit', $consumerReference);

            // Create MIT payment session record FIRST (required for webhook processing)
            $mitSession = JudopayMitPaymentSession::create([
                'subscription_id' => $subscription->id,
                'user_id' => $authorizedByUserId,
                'judopay_payment_reference' => $paymentReference,
                'amount' => $subscription->amount,
                'order_reference' => "MIT-{$subscription->id}",
                'description' => 'Direct MIT payment',
                'judopay_related_receipt_id' => $subscription->judopay_receipt_id,
                'card_token_used' => $subscription->card_token,
                'status' => 'created',
                'scheduled_for' => now(),
                'payment_completed_at' => null,
                'attempt_no' => 1,
            ]);

            Log::channel('judopay')->info('MIT session created for direct firing', [
                'session_id' => $mitSession->id,
                'payment_reference' => $paymentReference,
                'amount' => $subscription->amount,
            ]);

            // Extract fail-safe metadata using helper (wrapped in try-catch to prevent breaking MIT)
            try {
                $vrm = \App\Helpers\JudopayPayload::extractVrmFromSubscription($subscription);
            } catch (\Exception $e) {
                Log::channel('judopay')->warning('Failed to extract VRM for direct MIT', [
                    'subscription_id' => $subscriptionId,
                    'error' => $e->getMessage(),
                ]);
                $vrm = 'N/A';
            }

            $contractId = $subscription->subscribable_id ?? null;
            $contractType = $subscription->subscribable_type ? class_basename($subscription->subscribable_type) : 'N/A';

            // Prepare MIT payload using existing helper with fail-safe metadata
            $payload = \App\Helpers\JudopayPayload::prepareMitPayload(
                $subscription,
                $mitSession,
                null, // No invoice number for direct MIT
                $vrm,
                $contractId,
                $contractType,
                null // No ngn_mit_queue_id for direct MIT (not from queue)
            );

            Log::channel('judopay')->info('Direct MIT payload prepared', [
                'session_id' => $mitSession->id,
                'payment_reference' => $paymentReference,
                'amount' => $subscription->amount,
                'vrm' => $vrm,
                'contract_id' => $contractId,
                'contract_type' => $contractType,
            ]);

            // Make API call to JudoPay MIT endpoint
            $response = Http::withHeaders(JudopayService::getHeaders())
                ->post(JudopayService::getApiUrl(config('judopay.endpoints.transactions')), $payload);

            Log::channel('judopay')->info('Direct MIT API response', [
                'session_id' => $mitSession->id,
                'status_code' => $response->status(),
            ]);

            // Handle API response
            if ($response->successful()) {
                $responseData = $response->json();
                $apiResult = data_get($responseData, 'result');

                // Update MIT session with API response
                $mitSession->update([
                    'status' => $apiResult === 'Success' ? 'success' : 'created',
                    'judopay_response' => $responseData,
                    'judopay_receipt_id' => data_get($responseData, 'receiptId'),
                ]);

                // Create API outcome record
                try {
                    $outcomeData = \App\Helpers\JudopayPayload::mapOutcomeFields(
                        $responseData,
                        strtolower($apiResult ?? 'unknown'),
                        'api',
                        'App\Models\JudopayMitPaymentSession',
                        $mitSession->id,
                        $subscription->id
                    );

                    $outcome = \App\Models\JudopayPaymentSessionOutcome::create($outcomeData);

                    Log::channel('judopay')->info('Direct MIT API outcome created', [
                        'outcome_id' => $outcome->id,
                        'session_id' => $mitSession->id,
                        'api_result' => $apiResult,
                    ]);
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to create direct MIT API outcome', [
                        'error' => $e->getMessage(),
                    ]);
                }

                Log::channel('judopay')->info('Direct MIT fired successfully - awaiting webhook', [
                    'session_id' => $mitSession->id,
                    'payment_reference' => $paymentReference,
                    'api_result' => $apiResult,
                    'customer' => $subscription->judopayOnboarding->onboardable->first_name . ' ' . $subscription->judopayOnboarding->onboardable->last_name,
                ]);

                return [
                    'success' => true,
                    'message' => "MIT payment fired successfully for {$subscription->judopayOnboarding->onboardable->first_name} {$subscription->judopayOnboarding->onboardable->last_name}",
                    'data' => [
                        'session_id' => $mitSession->id,
                        'payment_reference' => $paymentReference,
                        'amount' => $subscription->amount,
                        'api_result' => $apiResult,
                        'customer_name' => $subscription->judopayOnboarding->onboardable->first_name . ' ' . $subscription->judopayOnboarding->onboardable->last_name,
                    ],
                ];

            } else {
                // API call failed - update session status
                $mitSession->update([
                    'status' => 'error',
                    'judopay_response' => $response->json(),
                    'failure_reason' => 'API call failed: ' . $response->status(),
                ]);

                Log::channel('judopay')->error('Direct MIT API call failed', [
                    'session_id' => $mitSession->id,
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'MIT API call failed: ' . $response->status(),
                    'data' => [
                        'session_id' => $mitSession->id,
                        'error_response' => $response->json(),
                    ],
                ];
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Direct MIT firing exception', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => "System error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Add NGN MIT Queue item to live firing chamber (judopay_mit_queues)
     * This authorises the payment to be processed automatically
     *
     * @param int $ngnMitQueueId The NGN MIT Queue item ID
     * @param int $authorizedByUserId The user ID authorising this payment
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public static function addToLiveChamber(int $ngnMitQueueId, int $authorizedByUserId): array
    {
        try {
            // Find the NGN MIT Queue item
            $ngnMitQueue = NgnMitQueue::with('subscribable')->find($ngnMitQueueId);

            if (!$ngnMitQueue) {
                Log::channel('judopay')->warning('NGN MIT Queue item not found', [
                    'ngn_mit_queue_id' => $ngnMitQueueId,
                ]);

                return [
                    'success' => false,
                    'message' => "Queue item #{$ngnMitQueueId} not found",
                ];
            }

            // Validate status is 'generated'
            if ($ngnMitQueue->status !== 'generated') {
                Log::channel('judopay')->warning('NGN MIT Queue item not in generated status', [
                    'ngn_mit_queue_id' => $ngnMitQueueId,
                    'current_status' => $ngnMitQueue->status,
                ]);

                return [
                    'success' => false,
                    'message' => "Queue item #{$ngnMitQueueId} is already {$ngnMitQueue->status}",
                ];
            }

            // Validate subscription exists and is active
            if (!$ngnMitQueue->subscribable) {
                Log::channel('judopay')->error('Subscription not found for NGN MIT Queue item', [
                    'ngn_mit_queue_id' => $ngnMitQueueId,
                    'subscribable_id' => $ngnMitQueue->subscribable_id,
                ]);

                return [
                    'success' => false,
                    'message' => "Subscription not found for queue item #{$ngnMitQueueId}",
                ];
            }

            $subscription = $ngnMitQueue->subscribable;

            // Generate payment reference
            $consumerReference = $subscription->consumer_reference ?? "SUB-{$subscription->id}";
            $paymentReference = JudopayService::generatePaymentReference('mit', $consumerReference);

            // Check if already exists in judopay_mit_queues (prevent duplicates)
            $existingLiveChamber = JudopayMitQueue::where('ngn_mit_queue_id', $ngnMitQueueId)->first();

            if ($existingLiveChamber) {
                Log::channel('judopay')->warning('Queue item already in live chamber', [
                    'ngn_mit_queue_id' => $ngnMitQueueId,
                    'judopay_mit_queue_id' => $existingLiveChamber->id,
                ]);

                return [
                    'success' => false,
                    'message' => "Queue item #{$ngnMitQueueId} is already in the live chamber",
                ];
            }

            // Create judopay_mit_queues record (live firing chamber)
            $judopayMitQueue = JudopayMitQueue::create([
                'ngn_mit_queue_id' => $ngnMitQueueId,
                'judopay_payment_reference' => $paymentReference,
                'mit_fire_date' => $ngnMitQueue->mit_fire_date, // Use config-driven date from NGN queue
                'retry' => 0,
                'fired' => false,
                'authorized_by' => $authorizedByUserId,
            ]);

            // Schedule individual MIT payment job at exact fire time (DATA-DRIVEN)
            \App\Jobs\ProcessSingleMitPaymentJob::dispatch($judopayMitQueue->id)
                ->delay($ngnMitQueue->mit_fire_date);

            // Create JudopayMitPaymentSession record (REQUIRED for webhook processing)
            JudopayMitPaymentSession::create([
                'subscription_id' => $subscription->id,
                'user_id' => $authorizedByUserId,
                'judopay_payment_reference' => $paymentReference,
                'amount' => $subscription->amount,
                'order_reference' => "MIT-{$subscription->id}",
                'description' => 'Automated recurring payment',
                'judopay_related_receipt_id' => $subscription->judopay_receipt_id,
                'card_token_used' => $subscription->card_token,
                'status' => 'created',
                'scheduled_for' => $ngnMitQueue->mit_fire_date,
                'payment_completed_at' => null,
                'attempt_no' => 1,
            ]);

            // Update NGN MIT Queue status to 'sent'
            $ngnMitQueue->update(['status' => 'sent']);

            Log::channel('judopay')->info('NGN MIT Queue item added to live chamber', [
                'ngn_mit_queue_id' => $ngnMitQueueId,
                'judopay_mit_queue_id' => $judopayMitQueue->id,
                'payment_reference' => $paymentReference,
                'mit_fire_date' => $ngnMitQueue->mit_fire_date->toDateTimeString(),
                'authorized_by' => $authorizedByUserId,
                'invoice_number' => $ngnMitQueue->invoice_number,
                'subscription_id' => $subscription->id,
                'mit_session_created' => true,
            ]);

            return [
                'success' => true,
                'message' => "Invoice {$ngnMitQueue->invoice_number} added to queue",
                'data' => [
                    'ngn_mit_queue_id' => $ngnMitQueueId,
                    'judopay_mit_queue_id' => $judopayMitQueue->id,
                    'payment_reference' => $paymentReference,
                    'invoice_number' => $ngnMitQueue->invoice_number,
                ],
            ];

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to add NGN MIT Queue item to live chamber', [
                'ngn_mit_queue_id' => $ngnMitQueueId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => "System error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Process a single MIT queue item from judopay_mit_queues
     * This method handles the actual payment processing for automated MIT firing
     *
     * @param \App\Models\JudopayMitQueue $mitQueueItem The MIT queue item to process
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public static function processMitQueueItem(JudopayMitQueue $mitQueueItem): array
    {
        try {
            Log::channel('judopay')->info('Processing MIT queue item', [
                'judopay_mit_queue_id' => $mitQueueItem->id,
                'ngn_mit_queue_id' => $mitQueueItem->ngn_mit_queue_id,
                'payment_reference' => $mitQueueItem->judopay_payment_reference,
                'retry_count' => $mitQueueItem->retry,
            ]);

            // Get the subscription from the NGN MIT queue
            $ngnMitQueue = $mitQueueItem->ngnMitQueue;
            if (!$ngnMitQueue || !$ngnMitQueue->subscribable) {
                return [
                    'success' => false,
                    'message' => 'NGN MIT Queue or subscription not found',
                ];
            }

            $subscription = $ngnMitQueue->subscribable;

            // Use the SAME payment reference (never create new ones)
            $paymentReference = $mitQueueItem->judopay_payment_reference;

            // Get the MIT payment session (created when item was added to live chamber)
            $mitSession = JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)->first();

            if (!$mitSession) {
                return [
                    'success' => false,
                    'message' => 'MIT payment session not found',
                ];
            }

            // Extract fail-safe metadata using helper (wrapped in try-catch to prevent breaking MIT)
            try {
                $vrm = \App\Helpers\JudopayPayload::extractVrmFromSubscription($subscription);
            } catch (\Exception $e) {
                Log::channel('judopay')->warning('Failed to extract VRM for MIT queue item', [
                    'judopay_mit_queue_id' => $mitQueueItem->id,
                    'error' => $e->getMessage(),
                ]);
                $vrm = 'N/A';
            }

            $invoiceNumber = $ngnMitQueue->invoice_number ?? 'N/A';
            $contractId = $subscription->subscribable_id ?? null;
            $contractType = $subscription->subscribable_type ? class_basename($subscription->subscribable_type) : 'N/A';

            // Prepare MIT payload using helper (eliminates code duplication)
            $payload = \App\Helpers\JudopayPayload::prepareMitPayload(
                $subscription,
                $mitSession,
                $invoiceNumber,
                $vrm,
                $contractId,
                $contractType,
                $mitQueueItem->ngn_mit_queue_id // Add for webhook compatibility
            );

            Log::channel('judopay')->info('MIT payload prepared for queue item', [
                'judopay_mit_queue_id' => $mitQueueItem->id,
                'payment_reference' => $paymentReference,
                'amount' => $mitSession->amount,
                'invoice_number' => $invoiceNumber,
                'vrm' => $vrm,
                'contract_id' => $contractId,
                'contract_type' => $contractType,
            ]);

            // Make API call to JudoPay MIT endpoint
            $response = Http::withHeaders(JudopayService::getHeaders())
                ->post(JudopayService::getApiUrl(config('judopay.endpoints.transactions')), $payload);

            Log::channel('judopay')->info('MIT API response for queue item', [
                'judopay_mit_queue_id' => $mitQueueItem->id,
                'status_code' => $response->status(),
            ]);

            // Handle API response - DON'T make final decisions here
            if ($response->successful()) {
                $responseData = $response->json();
                $apiResult = data_get($responseData, 'result');

                // Update mit_attempt based on current state (NOT based on retry count)
                // The ngn_mit_queues.mit_attempt is already set correctly by the webhook
                // We only update if it's still "not attempt" (shouldn't happen, but safety check)
                if ($ngnMitQueue->mit_attempt === 'not attempt') {
                    $ngnMitQueue->update([
                        'mit_attempt' => 'first', // Only update if still unset
                    ]);
                }

                // Update MIT session status to 'success' (API call successful)
                $mitSession = JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)->first();

                if ($mitSession) {
                    $mitSession->update([
                        'status' => 'success',
                        'payment_completed_at' => now(),
                        'attempt_no' => $mitQueueItem->retry + 1,
                        'judopay_response' => $responseData,
                    ]);
                }

                // Create API outcome (FIRST of two entries: api + webhook)
                try {
                    $subscriptionId = $subscription->id;
                    $sessionId = $mitSession ? $mitSession->id : null;

                    if ($sessionId) {
                        $outcomeData = \App\Helpers\JudopayPayload::mapOutcomeFields(
                            $responseData,
                            strtolower($apiResult ?? 'unknown'),
                            'api',
                            'App\Models\JudopayMitPaymentSession',
                            $sessionId,
                            $subscriptionId
                        );

                        $outcome = \App\Models\JudopayPaymentSessionOutcome::create($outcomeData);

                        Log::channel('judopay')->info('MIT API outcome created', [
                            'outcome_id' => $outcome->id,
                            'mit_session_id' => $sessionId,
                            'mit_queue_id' => $mitQueueItem->id,
                            'api_result' => $apiResult,
                            'source' => 'api',
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to create MIT API outcome', [
                        'error' => $e->getMessage(),
                    ]);
                }

                Log::channel('judopay')->info('MIT API call successful - waiting for webhook', [
                    'judopay_mit_queue_id' => $mitQueueItem->id,
                    'payment_reference' => $paymentReference,
                    'api_result' => $apiResult,
                    'mit_attempt' => $ngnMitQueue->mit_attempt,
                    'note' => 'Final status will be determined by webhook',
                ]);

                // FALLBACK: If API result is "Success", update immediately (webhook unreliable)
                // This prevents successful payments from showing as "DECLINED"
                if ($apiResult === 'Success') {
                    // Update BOTH ngn_mit_queues AND judopay_mit_queues
                    $ngnMitQueue->update([
                        'cleared' => true,
                        'cleared_at' => now(),
                    ]);

                    $mitQueueItem->update([
                        'cleared' => true,
                        'cleared_at' => now(),
                    ]);

                    Log::channel('judopay')->info('MIT payment SUCCESS - Updated immediately (webhook fallback)', [
                        'judopay_mit_queue_id' => $mitQueueItem->id,
                        'payment_reference' => $paymentReference,
                        'api_result' => $apiResult,
                        'cleared' => true,
                        'note' => 'Updated immediately due to unreliable webhooks',
                    ]);
                } elseif ($apiResult === 'Declined') {
                    $ngnMitQueue->update([
                        'cleared' => false,
                        'cleared_at' => null,
                    ]);

                    $mitQueueItem->update([
                        'cleared' => false,
                        'cleared_at' => null,
                    ]);

                    Log::channel('judopay')->warning('MIT payment DECLINED - Updated immediately (webhook fallback)', [
                        'judopay_mit_queue_id' => $mitQueueItem->id,
                        'payment_reference' => $paymentReference,
                        'api_result' => $apiResult,
                        'cleared' => false,
                        'note' => 'Updated immediately due to unreliable webhooks',
                    ]);
                }

                // Don't update cleared status yet - wait for webhook
                // The webhook will be the source of truth for final payment status

                return [
                    'success' => true,
                    'message' => 'MIT payment sent - waiting for webhook confirmation',
                    'data' => [
                        'judopay_mit_queue_id' => $mitQueueItem->id,
                        'payment_reference' => $paymentReference,
                        'api_result' => data_get($responseData, 'result'),
                        'status' => 'pending_webhook',
                    ],
                ];

            } else {
                // Update MIT session status to 'error' (API call failed)
                JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)
                    ->update([
                        'status' => 'error',
                        'attempt_no' => $mitQueueItem->retry + 1,
                        'judopay_response' => $response->json(),
                        'failure_reason' => 'API call failed: ' . $response->status(),
                    ]);

                Log::channel('judopay')->error('MIT API call failed for queue item', [
                    'judopay_mit_queue_id' => $mitQueueItem->id,
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'MIT API call failed: ' . $response->status(),
                    'data' => [
                        'judopay_mit_queue_id' => $mitQueueItem->id,
                        'error_response' => $response->json(),
                    ],
                ];
            }

        } catch (\Exception $e) {
            // Update MIT session status to 'error' (exception occurred)
            JudopayMitPaymentSession::where('judopay_payment_reference', $mitQueueItem->judopay_payment_reference)
                ->update([
                    'status' => 'error',
                    'attempt_no' => $mitQueueItem->retry + 1,
                    'failure_reason' => 'System error: ' . $e->getMessage(),
                ]);

            Log::channel('judopay')->error('MIT queue item processing exception', [
                'judopay_mit_queue_id' => $mitQueueItem->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => "System error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Process MIT queue webhook - Source of truth for MIT queue payments
     * This method handles webhook responses for MIT queue items
     *
     * @param array $receipt Webhook receipt data
     * @param string $status Payment status from webhook
     * @return void
     */
    public static function processMitQueueWebhook(array $receipt, string $status): void
    {
        try {
            $paymentReference = data_get($receipt, 'yourPaymentReference');
            $ngnMitQueueId = data_get($receipt, 'yourPaymentMetaData.ngn_mit_queue_id');

            Log::channel('judopay')->info('Processing MIT queue webhook', [
                'payment_reference' => $paymentReference,
                'ngn_mit_queue_id' => $ngnMitQueueId,
                'status' => $status,
                'receipt_id' => data_get($receipt, 'receiptId'),
            ]);

            // Find the MIT queue item
            $mitQueueItem = JudopayMitQueue::where('judopay_payment_reference', $paymentReference)->first();

            if (!$mitQueueItem) {
                Log::channel('judopay')->warning('MIT queue item not found for webhook', [
                    'payment_reference' => $paymentReference,
                    'status' => $status,
                ]);
                return;
            }

            $ngnMitQueue = $mitQueueItem->ngnMitQueue;
            if (!$ngnMitQueue) {
                Log::channel('judopay')->error('NGN MIT Queue not found for MIT queue item', [
                    'mit_queue_id' => $mitQueueItem->id,
                    'ngn_mit_queue_id' => $mitQueueItem->ngn_mit_queue_id,
                ]);
                return;
            }

            // Update based on webhook status (source of truth)
            if ($status === 'success') {
                // Payment successful
                $ngnMitQueue->update([
                    'mit_attempt' => $mitQueueItem->retry == 0 ? 'first' : 'second',
                    'cleared' => true,
                    'cleared_at' => now(),
                ]);

                // Also update THIS specific judopay_mit_queues record
                $mitQueueItem->update([
                    'cleared' => true,
                    'cleared_at' => now(),
                ]);

                // Update MIT session status to 'success'
                JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)
                    ->update([
                        'status' => 'success',
                        'payment_completed_at' => now(),
                        'judopay_response' => $receipt,
                    ]);

                Log::channel('judopay')->info('MIT queue payment SUCCESS - Webhook confirmed', [
                    'mit_queue_id' => $mitQueueItem->id,
                    'payment_reference' => $paymentReference,
                    'cleared' => true,
                    'receipt_id' => data_get($receipt, 'receiptId'),
                ]);

                // Create outcome record for reporting
                try {
                    $subscriptionId = data_get($receipt, 'yourPaymentMetaData.subscription_id') ?? $subscription->id;

                    // Get the JudopayMitPaymentSession ID for this payment
                    $mitSession = JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)->first();
                    $sessionId = $mitSession ? $mitSession->id : $mitQueueItem->id;

                    $outcomeData = \App\Helpers\JudopayPayload::mapOutcomeFields(
                        $receipt,
                        $status,
                        'webhook',
                        'App\Models\JudopayMitPaymentSession',
                        $sessionId,
                        $subscriptionId
                    );

                    $outcome = \App\Models\JudopayPaymentSessionOutcome::create($outcomeData);

                    Log::channel('judopay')->info('MIT queue outcome created', [
                        'outcome_id' => $outcome->id,
                        'mit_session_id' => $sessionId,
                        'mit_queue_id' => $mitQueueItem->id,
                        'receipt_id' => data_get($receipt, 'receiptId'),
                        'status' => $status,
                    ]);
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to create MIT outcome', [
                        'error' => $e->getMessage(),
                    ]);
                }

                // Send success notifications
                try {
                    $subscription = $ngnMitQueue->subscribable;
                    $customer = $subscription->subscribable?->customer ?? $subscription->customer;

                    // Send customer notification
                    if ($customer && $customer->email) {
                        $customer->notify(new \App\Notifications\MitSuccessCustomerNotification($mitQueueItem));
                    }

                    // Send internal notification to users with permission
                    $notifiableUsers = \App\Models\User::permission('can-receive-mit-notifications')->get();

                    foreach ($notifiableUsers as $user) {
                        $user->notify(new \App\Notifications\MitSuccessInternalNotification($mitQueueItem));
                    }
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to send MIT success notifications', [
                        'error' => $e->getMessage(),
                    ]);
                }

            } elseif ($status === 'declined') {
                // Payment declined - determine if retry is possible
                $currentAttempt = $ngnMitQueue->mit_attempt; // Use NGN queue attempt level, not retry count

                $ngnMitQueue->update([
                    'mit_attempt' => $currentAttempt,
                    'cleared' => false,
                    'cleared_at' => null,
                ]);

                // Update MIT session status to 'declined'
                JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)
                    ->update([
                        'status' => 'declined',
                        'judopay_response' => $receipt,
                        'failure_reason' => data_get($receipt, 'message'),
                    ]);

                // Create outcome record for reporting
                try {
                    $subscriptionId = data_get($receipt, 'yourPaymentMetaData.subscription_id') ?? $subscription->id;

                    // Get the JudopayMitPaymentSession ID for this payment
                    $mitSession = JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)->first();
                    $sessionId = $mitSession ? $mitSession->id : $mitQueueItem->id;

                    $outcomeData = \App\Helpers\JudopayPayload::mapOutcomeFields(
                        $receipt,
                        $status,
                        'webhook',
                        'App\Models\JudopayMitPaymentSession',
                        $sessionId,
                        $subscriptionId
                    );

                    $outcome = \App\Models\JudopayPaymentSessionOutcome::create($outcomeData);

                    Log::channel('judopay')->info('MIT queue outcome created', [
                        'outcome_id' => $outcome->id,
                        'mit_session_id' => $sessionId,
                        'mit_queue_id' => $mitQueueItem->id,
                        'receipt_id' => data_get($receipt, 'receiptId'),
                        'status' => $status,
                    ]);
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to create MIT outcome', [
                        'error' => $e->getMessage(),
                    ]);
                }

                // Check if retry is possible and enabled
                $retryConfig = config('judopay.mit.retry_system');

                if ($retryConfig['enabled'] && $currentAttempt === 'first') {
                    // First attempt failed - CREATE retry session immediately for next day at retry time
                    Log::channel('judopay')->info('MIT payment DECLINED - Scheduling retry', [
                        'mit_queue_id' => $mitQueueItem->id,
                        'payment_reference' => $paymentReference,
                        'current_attempt' => $currentAttempt,
                        'next_attempt' => 'second',
                        'decline_reason' => data_get($receipt, 'message'),
                        'retry_time' => $retryConfig['retry_time'],
                    ]);

                    // Update mit_attempt to 'second' BEFORE creating retry session
                    $ngnMitQueue->update(['mit_attempt' => 'second']);

                    // Create retry session immediately
                    $retryResult = self::createRetryMitSession($ngnMitQueue, 'second');

                    Log::channel('judopay')->info('MIT retry session created from webhook', [
                        'original_mit_queue_id' => $mitQueueItem->id,
                        'retry_result' => $retryResult,
                    ]);
                } else {
                    // Second attempt failed or retry disabled - mark for manual collection
                    $ngnMitQueue->update(['mit_attempt' => 'manual']);

                    Log::channel('judopay')->warning('MIT payment DECLINED - Marked for manual collection', [
                        'mit_queue_id' => $mitQueueItem->id,
                        'payment_reference' => $paymentReference,
                        'current_attempt' => $currentAttempt,
                        'decline_reason' => data_get($receipt, 'message'),
                        'action' => 'Marked for manual collection - max retries reached',
                    ]);
                }

                // Send failure notifications
                try {
                    $subscription = $ngnMitQueue->subscribable;
                    $customer = $subscription->subscribable?->customer ?? $subscription->customer;
                    $failureReason = data_get($receipt, 'message', 'Card declined');

                    // Send customer notification
                    if ($customer && $customer->email) {
                        $customer->notify(new \App\Notifications\MitFailureCustomerNotification($mitQueueItem, $failureReason));
                    }

                    // Send internal notification to users with permission
                    $notifiableUsers = \App\Models\User::permission('can-receive-mit-notifications')->get();

                    foreach ($notifiableUsers as $user) {
                        $user->notify(new \App\Notifications\MitFailureInternalNotification($mitQueueItem, $failureReason));
                    }
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to send MIT failure notifications', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('MIT queue webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'receipt' => $receipt,
            ]);
        }
    }

    /**
     * Create a new MIT session for retry (payment decline retry, not API error retry)
     * This creates a new MIT session with a new payment reference for failed payments
     *
     * @param \App\Models\NgnMitQueue $ngnMitQueue The original MIT queue item
     * @param string $nextAttempt The next attempt level ('second' or 'manual')
     * @return array
     */
    public static function createRetryMitSession(NgnMitQueue $ngnMitQueue, string $nextAttempt): array
    {
        try {
            Log::channel('judopay')->info('Creating MIT retry session', [
                'ngn_mit_queue_id' => $ngnMitQueue->id,
                'invoice_number' => $ngnMitQueue->invoice_number,
                'next_attempt' => $nextAttempt,
            ]);

            // Validate subscription exists
            if (!$ngnMitQueue->subscribable) {
                return [
                    'success' => false,
                    'message' => "Subscription not found for MIT queue item #{$ngnMitQueue->id}",
                ];
            }

            $subscription = $ngnMitQueue->subscribable;

            // Generate NEW payment reference for retry
            $consumerReference = $subscription->consumer_reference ?? "SUB-{$subscription->id}";
            $paymentReference = JudopayService::generatePaymentReference('mit', $consumerReference);

            // Check idempotency - ensure we don't create duplicate retry sessions
            $existingRetrySession = JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)->first();
            if ($existingRetrySession) {
                Log::channel('judopay')->warning('Retry session already exists', [
                    'payment_reference' => $paymentReference,
                    'existing_session_id' => $existingRetrySession->id,
                ]);

                return [
                    'success' => false,
                    'message' => "Retry session already exists for payment reference: {$paymentReference}",
                ];
            }

            // Get original MIT fire date from NGN queue (when first attempt was scheduled)
            $originalFireDate = Carbon::parse($ngnMitQueue->mit_fire_date);

            // Retry should happen at the SAME TIME as first attempt, but exactly 24 hours later
            $retryFireDate = $originalFireDate->copy()->addHours(24);

            // Ensure we're not scheduling in the past (safety check)
            if ($retryFireDate->isPast()) {
                // If somehow in past, add one more day
                $retryFireDate->addDay();
            }

            // Automated retry system - ALL retries are authorized by NGN SYSTEM (user 93)
            // This ensures automated queue operations are attributed to the system user
            $authorizedByUserId = 93;

            // Create new JudopayMitQueue record for retry
            $judopayMitQueue = JudopayMitQueue::create([
                'ngn_mit_queue_id' => $ngnMitQueue->id, // SAME ngn_mit_queue_id
                'judopay_payment_reference' => $paymentReference, // NEW payment reference
                'mit_fire_date' => $retryFireDate,
                'retry' => 0, // Reset retry count for new session
                'fired' => false,
                'authorized_by' => $authorizedByUserId, // NGN SYSTEM (93) - automated retry
            ]);

            // Schedule individual MIT retry job at exact retry time (DATA-DRIVEN)
            \App\Jobs\ProcessSingleMitPaymentJob::dispatch($judopayMitQueue->id)
                ->delay($retryFireDate);

            // Create new JudopayMitPaymentSession record for retry
            JudopayMitPaymentSession::create([
                'subscription_id' => $subscription->id,
                'user_id' => $authorizedByUserId, // NGN SYSTEM (93) - automated retry
                'judopay_payment_reference' => $paymentReference,
                'amount' => $subscription->amount,
                'order_reference' => "MIT-{$subscription->id}",
                'description' => 'Automated recurring payment (retry)',
                'judopay_related_receipt_id' => $subscription->judopay_receipt_id,
                'card_token_used' => $subscription->card_token,
                'status' => 'created',
                'scheduled_for' => $retryFireDate,
                'payment_completed_at' => null,
                'attempt_no' => $nextAttempt === 'second' ? 2 : 3,
            ]);

            // Update NGN MIT Queue attempt level
            $ngnMitQueue->update(['mit_attempt' => $nextAttempt]);

            Log::channel('judopay')->info('MIT retry session created successfully', [
                'ngn_mit_queue_id' => $ngnMitQueue->id,
                'judopay_mit_queue_id' => $judopayMitQueue->id,
                'payment_reference' => $paymentReference,
                'retry_fire_date' => $retryFireDate->toDateTimeString(),
                'next_attempt' => $nextAttempt,
                'invoice_number' => $ngnMitQueue->invoice_number,
            ]);

            return [
                'success' => true,
                'message' => "MIT retry session created for invoice {$ngnMitQueue->invoice_number}",
                'data' => [
                    'ngn_mit_queue_id' => $ngnMitQueue->id,
                    'judopay_mit_queue_id' => $judopayMitQueue->id,
                    'payment_reference' => $paymentReference,
                    'retry_fire_date' => $retryFireDate->toDateTimeString(),
                    'next_attempt' => $nextAttempt,
                ],
            ];

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to create MIT retry session', [
                'ngn_mit_queue_id' => $ngnMitQueue->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => "System error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Process Fire MIT webhook - handles webhooks for direct MIT payments
     * This method handles webhook responses for Fire MIT payment sessions
     *
     * @param JudopayMitPaymentSession $mitSession The MIT payment session
     * @param array $receipt Webhook receipt data
     * @param string $status Payment status from webhook
     * @return void
     */
    public static function processMitWebhook(JudopayMitPaymentSession $mitSession, array $receipt, string $status): void
    {
        try {
            $paymentReference = $mitSession->judopay_payment_reference;
            $subscription = JudopaySubscription::find($mitSession->subscription_id);

            Log::channel('judopay')->info('Processing Fire MIT webhook', [
                'session_id' => $mitSession->id,
                'payment_reference' => $paymentReference,
                'status' => $status,
                'receipt_id' => data_get($receipt, 'receiptId'),
                'subscription_id' => $subscription?->id,
            ]);

            if (!$subscription) {
                Log::channel('judopay')->error('Subscription not found for Fire MIT webhook', [
                    'session_id' => $mitSession->id,
                    'subscription_id' => $mitSession->subscription_id,
                ]);
                return;
            }

            // Update MIT session based on webhook status
            if ($status === 'success') {
                // Payment successful
                $mitSession->update([
                    'status' => 'success',
                    'payment_completed_at' => now(),
                    'judopay_response' => $receipt,
                    'judopay_receipt_id' => data_get($receipt, 'receiptId'),
                ]);

                Log::channel('judopay')->info('Fire MIT payment SUCCESS - Webhook confirmed', [
                    'session_id' => $mitSession->id,
                    'payment_reference' => $paymentReference,
                    'receipt_id' => data_get($receipt, 'receiptId'),
                    'amount' => $mitSession->amount,
                ]);

                // Create outcome record for reporting
                try {
                    $outcomeData = \App\Helpers\JudopayPayload::mapOutcomeFields(
                        $receipt,
                        $status,
                        'webhook',
                        'App\Models\JudopayMitPaymentSession',
                        $mitSession->id,
                        $subscription->id
                    );

                    $outcome = \App\Models\JudopayPaymentSessionOutcome::create($outcomeData);

                    Log::channel('judopay')->info('Fire MIT outcome created', [
                        'outcome_id' => $outcome->id,
                        'session_id' => $mitSession->id,
                        'receipt_id' => data_get($receipt, 'receiptId'),
                        'status' => $status,
                    ]);
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to create Fire MIT outcome', [
                        'error' => $e->getMessage(),
                    ]);
                }

                // Send success notifications
                try {
                    $customer = $subscription->subscribable?->customer ?? $subscription->customer;

                    // Send customer notification
                    if ($customer && $customer->email) {
                        $customer->notify(new \App\Notifications\FireMitSuccessCustomerNotification($mitSession, $subscription));
                    }

                    // Send internal notification to users with permission
                    $notifiableUsers = \App\Models\User::permission('can-receive-mit-notifications')->get();

                    foreach ($notifiableUsers as $user) {
                        $user->notify(new \App\Notifications\FireMitSuccessInternalNotification($mitSession, $subscription));
                    }

                    Log::channel('judopay')->info('Fire MIT success notifications sent', [
                        'session_id' => $mitSession->id,
                        'customer_email' => $customer?->email,
                        'notified_users_count' => $notifiableUsers->count(),
                    ]);
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to send Fire MIT success notifications', [
                        'session_id' => $mitSession->id,
                        'error' => $e->getMessage(),
                    ]);
                }

            } elseif ($status === 'declined') {
                // Payment declined
                $failureReason = data_get($receipt, 'message', 'Card declined');

                $mitSession->update([
                    'status' => 'declined',
                    'judopay_response' => $receipt,
                    'failure_reason' => $failureReason,
                ]);

                Log::channel('judopay')->warning('Fire MIT payment DECLINED - Webhook confirmed', [
                    'session_id' => $mitSession->id,
                    'payment_reference' => $paymentReference,
                    'decline_reason' => $failureReason,
                    'amount' => $mitSession->amount,
                ]);

                // Create outcome record for reporting
                try {
                    $outcomeData = \App\Helpers\JudopayPayload::mapOutcomeFields(
                        $receipt,
                        $status,
                        'webhook',
                        'App\Models\JudopayMitPaymentSession',
                        $mitSession->id,
                        $subscription->id
                    );

                    $outcome = \App\Models\JudopayPaymentSessionOutcome::create($outcomeData);

                    Log::channel('judopay')->info('Fire MIT decline outcome created', [
                        'outcome_id' => $outcome->id,
                        'session_id' => $mitSession->id,
                        'receipt_id' => data_get($receipt, 'receiptId'),
                        'status' => $status,
                    ]);
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to create Fire MIT decline outcome', [
                        'error' => $e->getMessage(),
                    ]);
                }

                // Send failure notifications
                try {
                    $customer = $subscription->subscribable?->customer ?? $subscription->customer;

                    // Send customer notification
                    if ($customer && $customer->email) {
                        $customer->notify(new \App\Notifications\FireMitFailureCustomerNotification($mitSession, $subscription, $failureReason));
                    }

                    // Send internal notification to users with permission
                    $notifiableUsers = \App\Models\User::permission('can-receive-mit-notifications')->get();

                    foreach ($notifiableUsers as $user) {
                        $user->notify(new \App\Notifications\FireMitFailureInternalNotification($mitSession, $subscription, $failureReason));
                    }

                    Log::channel('judopay')->info('Fire MIT failure notifications sent', [
                        'session_id' => $mitSession->id,
                        'customer_email' => $customer?->email,
                        'notified_users_count' => $notifiableUsers->count(),
                        'failure_reason' => $failureReason,
                    ]);
                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to send Fire MIT failure notifications', [
                        'session_id' => $mitSession->id,
                        'error' => $e->getMessage(),
                    ]);
                }

            } else {
                // Unknown status
                $mitSession->update([
                    'status' => 'error',
                    'judopay_response' => $receipt,
                    'failure_reason' => "Unknown webhook status: {$status}",
                ]);

                Log::channel('judopay')->warning('Fire MIT webhook with unknown status', [
                    'session_id' => $mitSession->id,
                    'payment_reference' => $paymentReference,
                    'status' => $status,
                ]);
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Fire MIT webhook processing exception', [
                'session_id' => $mitSession->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update session status to error
            $mitSession->update([
                'status' => 'error',
                'failure_reason' => 'Webhook processing error: ' . $e->getMessage(),
            ]);
        }
    }
}
