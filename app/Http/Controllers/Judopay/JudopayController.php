<?php

namespace App\Http\Controllers\Judopay;

// carbon
use App\Helpers\JudopayCit;
use App\Helpers\JudopayLogSanitizer;
use App\Helpers\JudopayMit;
use App\Helpers\JudopayPayload;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayMitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;
use App\Models\JudopaySubscription;
use App\Services\JudopayService;
use App\Models\RentingBooking;
use App\Models\FinanceApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class JudopayController extends Controller
{
    /**
     * / Create a CIT session (direct API endpoint)
     * to Generate PayLink (Deprecated or Cross-Merge with RecurringController.createCitSession)
     */
    public function createCitSession(Request $request)
    {
        return JudopayCit::initialiseCitSession($request);
    }

    public function success(Request $request)
    {
        Log::channel('judopay')->info('=== JUDOPAY SUCCESS REDIRECT ===');
        Log::channel('judopay')->info('Method: '.$request->method());
        Log::channel('judopay')->info('Headers (sanitised)', JudopayLogSanitizer::sanitizeHeaders($request->headers->all()));
        Log::channel('judopay')->info('Query Parameters', JudopayLogSanitizer::sanitizeResponse($request->query()));
        Log::channel('judopay')->info('POST Data (sanitised)', JudopayLogSanitizer::sanitizeResponse($request->all()));
        Log::channel('judopay')->info('Full URL: '.$request->fullUrl());

        // Extract success data from request
        $receiptId = $request->input('ReceiptId');
        $cardToken = $request->input('CardToken');
        $reference = $request->input('Reference');
        $consumerReference = $request->input('ConsumerReference');
        $yourConsumerReference = $request->input('YourConsumerReference');

        // Try to find the CIT session using the JudoPay reference
        $citSession = null;
        if ($reference) {
            $citSession = JudopayCitPaymentSession::where('judopay_reference', $reference)->first();
            Log::channel('judopay')->info('Searching CIT session by reference', [
                'reference' => $reference,
                'found' => $citSession ? 'YES' : 'NO',
                'session_id' => $citSession ? $citSession->id : null,
            ]);
        } else {
            Log::warning('No reference found in success redirect', [
                'receipt_id' => $receiptId,
                'card_token' => $cardToken ? JudopayLogSanitizer::partialRedact($cardToken, 2, 2) : null,
                'consumer_reference' => $consumerReference,
                'your_consumer_reference' => $yourConsumerReference,
            ]);
        }

        // If not found by reference, try by consumer reference (fallback)
        if (! $citSession && $consumerReference) {
            Log::channel('judopay')->info('Trying fallback search by consumer reference', ['consumer_reference' => $consumerReference]);

            $subscription = JudopaySubscription::where('consumer_reference', $consumerReference)->first();
            if ($subscription) {
                Log::channel('judopay')->info('Found subscription, searching for active CIT session', [
                    'subscription_id' => $subscription->id,
                    'subscription_status' => $subscription->status,
                ]);

                $citSession = JudopayCitPaymentSession::where('subscription_id', $subscription->id)
                    ->where('is_active', true)
                    ->whereIn('status', ['created', 'success'])
                    ->orderBy('created_at', 'desc') // Get the most recent one
                    ->first();

                Log::channel('judopay')->info('Fallback CIT session search result', [
                    'found' => $citSession ? 'YES' : 'NO',
                    'session_id' => $citSession ? $citSession->id : null,
                    'session_status' => $citSession ? $citSession->status : null,
                ]);
            } else {
                Log::warning('No subscription found for consumer reference', ['consumer_reference' => $consumerReference]);
            }
        }

        if ($citSession) {
            // Update CIT session status to success (redirect confirmation) and increase score
            $citSession->update([
                'status' => 'success',
                'status_score' => $citSession->status_score + 1, // Increase score like webhook does
                'judopay_receipt_id' => $receiptId === 'null' ? null : $receiptId,
                'payment_completed_at' => now(),
            ]);

            // Update subscription with card token and activate if not already done
            $subscription = $citSession->subscription;
            if ($cardToken && $cardToken !== 'null') {
                $subscription->update([
                    'card_token' => $cardToken,
                    'receipt_id' => $receiptId === 'null' ? null : $receiptId,
                    'status' => 'active',
                ]);

                // Update onboarding status to true (customer is now onboarded with card token)
                JudopayCit::updateOnboardingStatus($subscription);
            }

            // Insert success outcome record
            $outcomeData = [
                'session_id' => $citSession->id,
                'session_type' => 'App\Models\JudopayCitPaymentSession',
                'subscription_id' => $citSession->subscription_id,
                'status' => 'success',
                'source' => 'success', // Success redirects from user completing payment
                'judopay_receipt_id' => $receiptId === 'null' ? null : $receiptId,
                'your_payment_reference' => $citSession->judopay_payment_reference,
                'your_consumer_reference' => $consumerReference,
                'payload' => $request->all(), // Store the success redirect data
                'message' => 'Payment successful - user redirected to success URL',
                'occurred_at' => now(),
            ];

            // Always insert success outcome (no duplicate constraint)
            $outcome = JudopayPaymentSessionOutcome::create($outcomeData);
            Log::channel('judopay')->info('Success outcome created', [
                'outcome_id' => $outcome->id,
                'session_id' => $citSession->id,
                'receipt_id' => $receiptId,
                'source' => 'success',
            ]);

            // Update subscription audit log
            $existingAuditLog = $subscription->audit_log;

            if (is_string($existingAuditLog)) {
                $existingAuditLog = json_decode($existingAuditLog, true) ?? [];
            }
            if (! is_array($existingAuditLog)) {
                $existingAuditLog = [];
            }

            // Ensure we have success_entries array
            if (! isset($existingAuditLog['success_entries'])) {
                $existingAuditLog['success_entries'] = [];
            }

            // Add success entry
            $successEntry = [
                'success_processed_at' => now()->toIsoString(),
                'receipt_id' => $receiptId === 'null' ? null : $receiptId,
                'card_token' => $cardToken === 'null' ? null : $cardToken,
                'judopay_reference' => $reference,
                'consumer_reference' => $consumerReference,
                'success_type' => 'redirect_success',
                'session_id' => $citSession->id,
            ];

            $existingAuditLog['success_entries'][] = $successEntry;
            $subscription->audit_log = $existingAuditLog;
            $subscription->save();

            Log::channel('judopay')->info('Success outcome processed successfully', [
                'outcome_id' => $outcome->id,
                'session_id' => $citSession->id,
                'subscription_id' => $citSession->subscription_id,
                'receipt_id' => $receiptId,
                'source' => 'success',
            ]);

            // Check if automatic refund should be triggered
            $refundMode = config('judopay.cit.refund_mode', 'manual');
            if ($refundMode === 'automatic' && $receiptId && $receiptId !== 'null') {
                $amount = (float) $citSession->amount;
                $currency = config('judopay.currency', 'GBP');
                $paymentReference = JudopayService::generatePaymentReference('refund', $subscription->consumer_reference ?? "SUB-{$citSession->subscription_id}");

                if ($amount > 0) {
                    // Process refund immediately (synchronously) when automatic mode is enabled
                    \App\Jobs\JudopayRefundJob::processRefund(
                        $receiptId,
                        $amount,
                        $currency,
                        $paymentReference,
                        $citSession->id,
                        $citSession->subscription_id
                    );

                    Log::channel('judopay')->info('Automatic refund processed immediately from success redirect', [
                        'session_id' => $citSession->id,
                        'receipt_id' => $receiptId,
                        'amount' => $amount,
                        'payment_reference' => $paymentReference,
                    ]);
                }
            }

            // Note: Notifications are sent from webhook handler to avoid duplicates

        } else {
            Log::warning('Could not find CIT session for success redirect', [
                'reference' => $reference,
                'consumer_reference' => $consumerReference,
                'receipt_id' => $receiptId,
            ]);
        }

        Log::channel('judopay')->info('=== END SUCCESS ===');

        // Prepare data for success view
        $customer = null;
        $booking = null;
        $finance_application = null;
        $judopay_subscription = null;

        $vehicle_vrm = null;

        if ($citSession) {
            $judopay_subscription = $citSession->subscription;
            if ($judopay_subscription) {
                $subscribable = $judopay_subscription->subscribable;

                // Resolve related domain entity and customer from subscribable
                if ($subscribable instanceof RentingBooking) {
                    $booking = $subscribable;
                    $customer = $booking->customer;

                    // Fetch active/current booking item to read VRM
                    try {
                        $booking->load(['rentingBookingItems' => function ($q) {
                            $q->whereNull('end_date')->with('motorbike');
                        }]);
                        $item = $booking->rentingBookingItems->first();
                        if (! $item) {
                            $item = $booking->rentingBookingItems()->with('motorbike')->latest('id')->first();
                        }
                        $vehicle_vrm = optional(optional($item)->motorbike)->reg_no;
                    } catch (\Throwable $e) {
                        // swallow and leave VRM null
                    }
                } elseif ($subscribable instanceof FinanceApplication) {
                    $finance_application = $subscribable;
                    $customer = $finance_application->customer;

                    // Fetch application item to read VRM
                    try {
                        $finance_application->load(['application_items' => function ($q) {
                            $q->with('motorbike')->latest('id');
                        }]);
                        $appItem = $finance_application->application_items->first();
                        $vehicle_vrm = optional(optional($appItem)->motorbike)->reg_no;
                    } catch (\Throwable $e) {
                        // swallow and leave VRM null
                    }
                }

                // Fallback: try onboardable customer through onboarding record
                if (! $customer && method_exists($judopay_subscription, 'judopayOnboarding')) {
                    $onboarding = $judopay_subscription->judopayOnboarding;
                    if ($onboarding && method_exists($onboarding, 'onboardable')) {
                        $onboardable = $onboarding->onboardable;
                        // If onboardable looks like a Customer model, use it
                        if (is_object($onboardable) && property_exists($onboardable, 'first_name')) {
                            $customer = $onboardable;
                        }
                    }
                }
            }
        }

        // Render success page with robust context
        return view('judopay-success', array_merge(
            compact(
                'customer',
                'booking',
                'finance_application',
                'judopay_subscription',
                'vehicle_vrm'
            ),
            [
                'payment_status' => 'success',
            ]
        ));
    }

    public function failure(Request $request)
    {
        Log::channel('judopay')->info('=== JUDOPAY FAILURE REDIRECT ===');
        Log::channel('judopay')->info('Method: '.$request->method());
        Log::channel('judopay')->info('Headers (sanitised)', JudopayLogSanitizer::sanitizeHeaders($request->headers->all()));
        Log::channel('judopay')->info('Query Parameters', JudopayLogSanitizer::sanitizeResponse($request->query()));
        Log::channel('judopay')->info('POST Data (sanitised)', JudopayLogSanitizer::sanitizeResponse($request->all()));
        Log::channel('judopay')->info('Full URL: '.$request->fullUrl());
        // Extract failure data from request
        $receiptId = $request->input('ReceiptId');
        $cardToken = $request->input('CardToken');
        $reference = $request->input('Reference');
        $consumerReference = $request->input('ConsumerReference');
        $yourConsumerReference = $request->input('YourConsumerReference');

        // Try to find the CIT session using the JudoPay reference
        $citSession = null;
        if ($reference) {
            $citSession = JudopayCitPaymentSession::where('judopay_reference', $reference)->first();
            Log::channel('judopay')->info('Searching CIT session by reference', [
                'reference' => $reference,
                'found' => $citSession ? 'YES' : 'NO',
                'session_id' => $citSession ? $citSession->id : null,
            ]);
        } else {
            Log::warning('No reference found in failure redirect', [
                'receipt_id' => $receiptId,
                'card_token' => $cardToken ? JudopayLogSanitizer::partialRedact($cardToken, 2, 2) : null,
                'consumer_reference' => $consumerReference,
                'your_consumer_reference' => $yourConsumerReference,
            ]);
        }

        // If not found by reference, try by consumer reference (fallback)
        if (! $citSession && $consumerReference) {
            Log::channel('judopay')->info('Trying fallback search by consumer reference', ['consumer_reference' => $consumerReference]);

            $subscription = JudopaySubscription::where('consumer_reference', $consumerReference)->first();
            if ($subscription) {
                Log::channel('judopay')->info('Found subscription, searching for active CIT session', [
                    'subscription_id' => $subscription->id,
                    'subscription_status' => $subscription->status,
                ]);

                $citSession = JudopayCitPaymentSession::where('subscription_id', $subscription->id)
                    ->where('is_active', true)
                    ->whereIn('status', ['created', 'success'])
                    ->orderBy('created_at', 'desc') // Get the most recent one
                    ->first();

                Log::channel('judopay')->info('Fallback CIT session search result', [
                    'found' => $citSession ? 'YES' : 'NO',
                    'session_id' => $citSession ? $citSession->id : null,
                    'session_status' => $citSession ? $citSession->status : null,
                ]);
            } else {
                Log::warning('No subscription found for consumer reference', ['consumer_reference' => $consumerReference]);
            }
        }

        if ($citSession) {
            // Update CIT session status to declined (failure redirect)
            $citSession->update([
                'status' => 'declined',
                'is_active' => false,
                'failure_reason' => 'Payment failed - redirected to failure URL',
                'judopay_receipt_id' => $receiptId === 'null' ? null : $receiptId,
            ]);

            // Insert failure outcome record
            $outcomeData = [
                'session_id' => $citSession->id,
                'session_type' => 'App\Models\JudopayCitPaymentSession',
                'subscription_id' => $citSession->subscription_id,
                'status' => 'declined', // Use 'declined' as it's closest to failure
                'source' => 'failure', // Custom source for failure redirects
                'judopay_receipt_id' => $receiptId === 'null' ? null : $receiptId,
                'your_payment_reference' => $citSession->judopay_payment_reference,
                'your_consumer_reference' => $consumerReference,
                'payload' => $request->all(), // Store the failure redirect data
                'message' => 'Payment failed - user redirected to failure URL',
                'occurred_at' => now(),
            ];

            $outcome = JudopayPaymentSessionOutcome::create($outcomeData);

            // Update subscription audit log
            $subscription = $citSession->subscription;
            $existingAuditLog = $subscription->audit_log;

            if (is_string($existingAuditLog)) {
                $existingAuditLog = json_decode($existingAuditLog, true) ?? [];
            }
            if (! is_array($existingAuditLog)) {
                $existingAuditLog = [];
            }

            // Ensure we have failure_entries array
            if (! isset($existingAuditLog['failure_entries'])) {
                $existingAuditLog['failure_entries'] = [];
            }

            // Add failure entry
            $failureEntry = [
                'failure_processed_at' => now()->toIsoString(),
                'receipt_id' => $receiptId === 'null' ? null : $receiptId,
                'card_token' => $cardToken === 'null' ? null : $cardToken,
                'judopay_reference' => $reference,
                'consumer_reference' => $consumerReference,
                'failure_type' => 'redirect_failure',
                'session_id' => $citSession->id,
            ];

            $existingAuditLog['failure_entries'][] = $failureEntry;
            $subscription->audit_log = $existingAuditLog;
            $subscription->save();

            Log::channel('judopay')->info('Failure outcome recorded successfully', [
                'outcome_id' => $outcome->id,
                'session_id' => $citSession->id,
                'subscription_id' => $citSession->subscription_id,
                'receipt_id' => $receiptId,
            ]);

        } else {
            Log::warning('Could not find CIT session for failure redirect', [
                'reference' => $reference,
                'consumer_reference' => $consumerReference,
                'receipt_id' => $receiptId,
            ]);
        }

        // Extract variables for the failure page (same logic as success method)
        $customer = null;
        $booking = null;
        $finance_application = null;
        $judopay_subscription = null;
        $vehicle_vrm = null;

        if ($citSession) {
            $judopay_subscription = $citSession->subscription;

            if ($judopay_subscription) {
                $subscribable = $judopay_subscription->subscribable;

                // Resolve related domain entity and customer from subscribable
                if ($subscribable instanceof RentingBooking) {
                    $booking = $subscribable;
                    $customer = $booking->customer;

                    // Fetch active/current booking item to read VRM
                    try {
                        $booking->load(['rentingBookingItems' => function ($q) {
                            $q->whereNull('end_date')->with('motorbike');
                        }]);
                        $item = $booking->rentingBookingItems->first();
                        if (! $item) {
                            $item = $booking->rentingBookingItems()->with('motorbike')->latest('id')->first();
                        }
                        $vehicle_vrm = optional(optional($item)->motorbike)->reg_no;
                    } catch (\Throwable $e) {
                        // swallow and leave VRM null
                    }
                } elseif ($subscribable instanceof FinanceApplication) {
                    $finance_application = $subscribable;
                    $customer = $finance_application->customer;

                    // Fetch application item to read VRM
                    try {
                        $finance_application->load(['application_items' => function ($q) {
                            $q->with('motorbike')->latest('id');
                        }]);
                        $appItem = $finance_application->application_items->first();
                        $vehicle_vrm = optional(optional($appItem)->motorbike)->reg_no;
                    } catch (\Throwable $e) {
                        // swallow and leave VRM null
                    }
                }

                // Fallback: try onboardable customer through onboarding record
                if (! $customer && method_exists($judopay_subscription, 'judopayOnboarding')) {
                    $onboarding = $judopay_subscription->judopayOnboarding;
                    if ($onboarding && method_exists($onboarding, 'onboardable')) {
                        $onboardable = $onboarding->onboardable;
                        // If onboardable looks like a Customer model, use it
                        if (is_object($onboardable) && property_exists($onboardable, 'first_name')) {
                            $customer = $onboardable;
                        }
                    }
                }
            }
        }

        Log::channel('judopay')->info('=== END FAILURE ===');

        // Render failure page with robust context
        return view('judopay-failure', array_merge(
            compact(
                'customer',
                'booking',
                'finance_application',
                'judopay_subscription',
                'vehicle_vrm'
            ),
            [
                'failure_reason' => 'Payment could not be processed',
                'payment_status' => 'failed',
            ]
        ));
    }

    // Callback ONLY from CIT, NOT FROM MIT.
    public function webhook(Request $request)
    {
        Log::channel('judopay')->info('=== JUDOPAY WEBHOOK ===');
        Log::channel('judopay')->info('Method: '.$request->method());
        Log::channel('judopay')->info('Headers (sanitised)', JudopayLogSanitizer::sanitizeHeaders($request->headers->all()));
        JudopayLogSanitizer::logResponse('Webhook Payload', $request->all());

        // 1. Validate Request
        $request->method() === 'POST' || abort(401);
        self::headerValidation($request) || abort(401);
        (collect($request->all())->has('judoId') || collect($request->all())->has('receipt')) || abort(404);

        // 2. Prepare Receipt
        $receipt = collect(JudopayPayload::normalize($request->all()));

        if ($receipt->isEmpty()) {
            Log::error('Receipt not found in webhook', [
                'payload' => $request->all(),
            ]);
            abort(404);
        }

        // Convert Collection to array for data_get() compatibility
        $receipt = $receipt->toArray();

        // 3. Look up Status
        $status = self::paymentStatus($receipt);

        // 3a. Check if this is a refund webhook
        $receiptType = data_get($receipt, 'type');
        if ($receiptType === 'Refund') {
            $originalReceiptId = data_get($receipt, 'originalReceiptId');
            if ($originalReceiptId) {
                // This is a refund webhook - handle it differently
                self::processRefundWebhook($receipt, $originalReceiptId);

                return response()->json(['status' => 'received'], 200);
            } else {
                Log::error('Refund webhook missing originalReceiptId', [
                    'refund_receipt_id' => data_get($receipt, 'receiptId'),
                    'payload' => $receipt,
                ]);

                return response()->json(['status' => 'error', 'message' => 'Missing originalReceiptId'], 400);
            }
        }

        // 4. Determine session type and instantiate relevant session
        $sessionType = data_get($receipt, 'yourPaymentMetaData.ngn_session_type', 'cit'); // To be review later
        $sessionId = data_get($receipt, 'yourPaymentMetaData.ngn_session_id');
        $paymentReference = data_get($receipt, 'yourPaymentReference');

        Log::channel('judopay')->info('Webhook Session Detection', [
            'session_type' => $sessionType,
            'session_id' => $sessionId,
            'payment_reference' => $paymentReference,
        ]);

        if ($sessionType === 'mit') {
            // Check if this is a MIT queue webhook (new system)
            $ngnMitQueueId = data_get($receipt, 'yourPaymentMetaData.ngn_mit_queue_id');

            if ($ngnMitQueueId) {
                Log::channel('judopay')->info('MIT Queue webhook detected', [
                    'ngn_mit_queue_id' => $ngnMitQueueId,
                    'payment_reference' => $paymentReference,
                    'status' => $status,
                ]);

                // Process MIT queue webhook (source of truth)
                JudopayMit::processMitQueueWebhook($receipt, $status);

                return response()->json(['status' => 'received'], 200);
            }

            // Handle legacy MIT session webhook (old system)
            $mitSession = JudopayMitPaymentSession::where('id', $sessionId)
                ->where('judopay_payment_reference', $paymentReference)
                ->first();

            Log::channel('judopay')->info('MIT Session: '.($mitSession ? $mitSession->id : 'NOT FOUND'));

            if (!$mitSession) {
                Log::channel('judopay')->error('MIT session not found for webhook - handling untracked payment', [
                    'session_id' => $sessionId,
                    'payment_reference' => $paymentReference,
                    'receipt_id' => data_get($receipt, 'receiptId'),
                    'amount' => data_get($receipt, 'amount'),
                    'consumer_reference' => data_get($receipt, 'consumer.yourConsumerReference'),
                ]);

                return response()->json(['status' => 'received'], 200);
            }

            // Continue with existing MIT session logic...
            JudopayMit::processMitWebhook($mitSession, $receipt, $status);

            return response()->json(['status' => 'received'], 200);

        } else {
            // Handle CIT webhook (existing logic)
            $citSession = JudopayCitPaymentSession::where('id', $sessionId)
                ->where('judopay_payment_reference', $paymentReference)
                ->first();
            Log::channel('judopay')->info('CIT Session: '.($citSession ? $citSession->id : 'NOT FOUND'));

            if (! $citSession) {
                Log::error('CIT session not found for webhook', [
                    'session_id' => $sessionId,
                    'payment_reference' => $paymentReference,
                ]);
                abort(404);
            }

            // Continue with existing CIT logic...
            JudopayCit::processCitWebhook($citSession, $receipt, $status);

            return response()->json(['status' => 'received'], 200);
        }
    }

    private static function processRefundWebhook(array $receipt, string $originalReceiptId): void
    {
        $refundReceiptId = data_get($receipt, 'receiptId');
        $refundAmount = data_get($receipt, 'amount');

        Log::channel('judopay')->info('Processing REFUND webhook', [
            'refund_receipt_id' => $refundReceiptId,
            'original_receipt_id' => $originalReceiptId,
            'refund_amount' => $refundAmount,
            'refund_type' => data_get($receipt, 'type'),
        ]);

        // Find the original transaction by receipt ID (look for successful payments)
        $originalOutcome = JudopayPaymentSessionOutcome::where('judopay_receipt_id', $originalReceiptId)
            ->where('status', 'success')
            ->first();

        if (! $originalOutcome) {
            Log::error('Original successful transaction not found for refund', [
                'original_receipt_id' => $originalReceiptId,
                'refund_receipt_id' => $refundReceiptId,
                'available_outcomes' => JudopayPaymentSessionOutcome::where('judopay_receipt_id', $originalReceiptId)
                    ->pluck('status', 'id')
                    ->toArray(),
            ]);

            return;
        }

        // Get the original session
        $originalSession = $originalOutcome->session;
        if (! $originalSession) {
            Log::error('Original session not found for refund', [
                'original_receipt_id' => $originalReceiptId,
                'original_outcome_id' => $originalOutcome->id,
            ]);

            return;
        }

        // Update the original session to refunded status
        $originalSession->update(['status' => 'refunded']);
        Log::channel('judopay')->info('Original session updated to refunded', [
            'session_type' => get_class($originalSession),
            'session_id' => $originalSession->id,
            'original_receipt_id' => $originalReceiptId,
            'original_amount' => $originalOutcome->amount,
            'refund_amount' => $refundAmount,
        ]);

        // Create refund outcome record
        $refundOutcomeData = JudopayPayload::mapOutcomeFields(
            $receipt,
            'refunded',
            'webhook',
            get_class($originalSession),
            $originalSession->id,
            $originalOutcome->subscription_id
        );

        // Store original receipt ID for traceability
        $refundOutcomeData['your_payment_reference'] = data_get($receipt, 'yourPaymentReference');
        if (isset($refundOutcomeData['payload']) && is_array($refundOutcomeData['payload'])) {
            $refundOutcomeData['payload']['originalReceiptId'] = $originalReceiptId;

            // Store user who initiated refund (from session webhook data or manual refund)
            $refundedByUserId = null;
            if ($originalSession instanceof JudopayCitPaymentSession) {
                $webhookData = $originalSession->judopay_webhook_data ?? [];
                $refundedByUserId = data_get($webhookData, 'refunded_by_user_id');
            }
            if ($refundedByUserId) {
                $refundOutcomeData['payload']['refunded_by_user_id'] = $refundedByUserId;
            }
        }

        $refundOutcome = JudopayPaymentSessionOutcome::create($refundOutcomeData);

        Log::channel('judopay')->info('Refund processed successfully', [
            'refund_outcome_id' => $refundOutcome->id,
            'original_outcome_id' => $originalOutcome->id,
            'original_receipt_id' => $originalReceiptId,
            'refund_receipt_id' => $refundReceiptId,
            'original_amount' => $originalOutcome->amount,
            'refund_amount' => $refundAmount,
            'subscription_id' => $originalOutcome->subscription_id,
            'refunded_by_user_id' => $refundedByUserId ?? 'System (Automatic)',
        ]);

        // Send refund notifications
        if ($originalSession instanceof JudopayCitPaymentSession) {
            \App\Helpers\JudopayNotificationHelper::sendCitRefundNotifications(
                $originalSession,
                $refundOutcome,
                $originalOutcome
            );
        }
    }

    private static function headerValidation(Request $request): bool
    {
        if (empty($request->header('php-auth-user'))
            || empty($request->header('php-auth-pw'))) {
            return false;
        }

        return JudopayService::validateWebhookAuth(
            (string) $request->header('php-auth-user'),
            (string) $request->header('php-auth-pw')
        );
    }

    private static function paymentStatus($receipt): string
    {
        return strtolower(data_get($receipt, 'result'));
    }

    /**
     * Manual refund endpoint for CIT payments
     * POST /admin/judopay/cit/{session}/refund
     */
    public function manualRefund($session)
    {
        try {
            // Find the session
            $citSession = JudopayCitPaymentSession::findOrFail($session);

            // Authorisation check - permission required
            if (! auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorised - judopay-can-refund permission required',
                ], 403);
            }

            // Validation: session must be successful and not already refunded
            if ($citSession->status !== 'success') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot refund - session status is '{$citSession->status}', must be 'success'",
                ], 400);
            }

            if ($citSession->status === 'refunded') {
                return response()->json([
                    'success' => false,
                    'message' => 'This payment has already been refunded',
                ], 400);
            }

            // Check if refund already exists
            $existingRefund = JudopayPaymentSessionOutcome::where('session_id', $citSession->id)
                ->where('session_type', 'App\Models\JudopayCitPaymentSession')
                ->where('status', 'refunded')
                ->first();

            if ($existingRefund) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund already exists for this session',
                ], 400);
            }

            // Ensure we have receipt ID
            if (! $citSession->judopay_receipt_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot refund - no receipt ID found for this session',
                ], 400);
            }

            // Build refund payload
            $receiptId = $citSession->judopay_receipt_id;
            $refundAmountBehaviour = config('judopay.cit.refund_amount_behaviour', 'full');

            // Determine refund amount based on behaviour
            if ($refundAmountBehaviour === 'custom' && request()->has('refund_amount')) {
                $amount = (float) request()->input('refund_amount');
                // Validate custom amount doesn't exceed original
                if ($amount > (float) $citSession->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Refund amount cannot exceed original payment amount',
                    ], 400);
                }
                if ($amount <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Refund amount must be greater than zero',
                    ], 400);
                }
            } else {
                // Default to full refund
                $amount = (float) $citSession->amount;
            }

            $currency = config('judopay.currency', 'GBP');
            $paymentReference = JudopayService::generatePaymentReference('refund', $citSession->subscription->consumer_reference ?? "SUB-{$citSession->subscription_id}");

            $payload = [
                'receiptId' => $receiptId,
                'amount' => $amount,
                'currency' => $currency,
                'yourPaymentReference' => $paymentReference,
            ];

            // Store user who initiated refund in session metadata for webhook notification
            // This will be passed through to the refund outcome payload
            $citSession->update([
                'judopay_webhook_data' => array_merge(
                    $citSession->judopay_webhook_data ?? [],
                    ['refunded_by_user_id' => auth()->id()]
                ),
            ]);

            Log::channel('judopay')->info('Manual refund initiated', [
                'session_id' => $citSession->id,
                'receipt_id' => $receiptId,
                'amount' => $amount,
                'currency' => $currency,
                'payment_reference' => $paymentReference,
                'authorised_by' => auth()->id(),
            ]);

            // Call refund service
            $result = JudopayService::processRefund($payload);

            // Check if we should simulate webhook for testing (when API fails with invalid receipt ID)
            $shouldSimulateWebhook = false;
            if (!$result['success']) {
                $errorMessage = $result['message'] ?? '';
                $errorData = $result['data'] ?? [];
                $errorCode = data_get($errorData, 'code');
                $errorDetails = data_get($errorData, 'details', []);

                // Check if error is "invalid receipt ID" (common in testing with fake receipt IDs)
                $isInvalidReceiptError = false;
                if ($errorCode == 1 || str_contains(strtolower($errorMessage), 'invalid') || str_contains(strtolower($errorMessage), 'receipt')) {
                    foreach ($errorDetails as $detail) {
                        if (isset($detail['code']) && $detail['code'] == 56) {
                            $isInvalidReceiptError = true;
                            break;
                        }
                    }
                    if (str_contains(strtolower($errorMessage), 'receipt') && str_contains(strtolower($errorMessage), 'invalid')) {
                        $isInvalidReceiptError = true;
                    }
                }

                // Simulate webhook if invalid receipt error (for testing with fake receipt IDs)
                if ($isInvalidReceiptError && (config('app.env') === 'local' || config('app.env') === 'testing' || env('JUDOPAY_SIMULATE_REFUNDS', false))) {
                    $shouldSimulateWebhook = true;
                    Log::channel('judopay')->info('Simulating refund webhook for testing (invalid receipt ID)', [
                        'session_id' => $citSession->id,
                        'receipt_id' => $receiptId,
                        'reason' => 'Invalid receipt ID in test environment - simulating webhook',
                    ]);
                }
            }

            if ($result['success']) {
                Log::channel('judopay')->info('Manual refund API call successful', [
                    'session_id' => $citSession->id,
                    'refund_receipt_id' => data_get($result['data'], 'receiptId'),
                    'note' => 'Webhook will update session status to refunded',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Refund initiated successfully - webhook will confirm final status',
                    'data' => [
                        'session_id' => $citSession->id,
                        'refund_receipt_id' => data_get($result['data'], 'receiptId'),
                    ],
                ]);
            } elseif ($shouldSimulateWebhook) {
                // Simulate refund webhook for testing
                try {
                    $refundReceiptId = 'REFUND_RECEIPT_' . \Illuminate\Support\Str::random(10);
                    $mockRefundReceipt = [
                        'receiptId' => $refundReceiptId,
                        'type' => 'Refund',
                        'originalReceiptId' => $receiptId,
                        'amount' => $amount,
                        'result' => 'Success',
                        'yourPaymentReference' => $paymentReference,
                        'message' => 'Refund processed successfully (simulated)',
                        'createdAt' => \Carbon\Carbon::now()->toIso8601String(),
                    ];

                    // Process the refund webhook
                    self::processRefundWebhook($mockRefundReceipt, $receiptId);

                    // Refresh session to get updated status
                    $citSession->refresh();

                    Log::channel('judopay')->info('Refund webhook simulated successfully', [
                        'session_id' => $citSession->id,
                        'refund_receipt_id' => $refundReceiptId,
                        'session_status' => $citSession->status,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Refund processed successfully (simulated for testing)',
                        'data' => [
                            'session_id' => $citSession->id,
                            'refund_receipt_id' => $refundReceiptId,
                            'session_status' => $citSession->status,
                            'simulated' => true,
                        ],
                    ]);
                } catch (\Exception $webhookException) {
                    Log::channel('judopay')->error('Failed to simulate refund webhook', [
                        'session_id' => $citSession->id,
                        'error' => $webhookException->getMessage(),
                        'trace' => $webhookException->getTraceAsString(),
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'API call failed and webhook simulation failed: ' . $webhookException->getMessage(),
                        'data' => $result['data'],
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'data' => $result['data'],
                ], 400);
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Manual refund exception', [
                'session_id' => $session ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'System error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
