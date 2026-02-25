<?php

// app/Http/Controllers/PayPalWebhookController.php

namespace App\Http\Controllers;

use App\Mail\Ecommerce\InvalidPayPalWebhookSignatureMailer;
use App\Mail\Ecommerce\NewOrderProcessMailer;
use App\Mail\Ecommerce\OrderProcessMailer;
use App\Mail\Ecommerce\OrderRefundMailer;
use App\Mail\Ecommerce\PayPalWebhookAnomalyMailer;
use App\Models\Ecommerce\EcOrder;
use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use App\Models\PaymentsPaypal;
use App\Models\PaypalWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('handle:PayPal Webhook Request:', $request->all());

        // 1. First verify PayPal signature before processing anything
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $tokenResponse = $provider->getAccessToken();

            if (! isset($tokenResponse['access_token'])) {
                Log::error('Failed to retrieve PayPal access token.', ['response' => $tokenResponse]);
                $this->notifyAnomaly('ACCESS_TOKEN_ERROR', null, null, null, null, ['response' => $tokenResponse], 'Failed to retrieve PayPal access token');

                return response()->json(['message' => 'Failed to retrieve access token'], 500);
            }

            // Verify webhook signature first
            $verificationPayload = [
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url' => $request->header('PAYPAL-CERT-URL'),
                'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'webhook_id' => config('paypal.webhook_id'),
                'webhook_event' => json_decode($request->getContent(), true),
            ];

            if ($this->isDebugMode()) {
                Log::info('PayPal Webhook Debug', [
                    'headers' => $request->headers->all(),
                    'payload' => $request->all(),
                    'verification_payload' => $verificationPayload,
                ]);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$tokenResponse['access_token'],
            ])->post($this->getWebhookVerificationUrl(), $verificationPayload);

            $verificationData = $response->json();

            if (! $response->successful() || $verificationData['verification_status'] !== 'SUCCESS') {
                Log::warning('Invalid PayPal webhook signature.', [
                    'verification_status' => $verificationData['verification_status'] ?? 'UNKNOWN',
                ]);
                $this->notifyAnomaly('INVALID_SIGNATURE', null, null, null, null,
                    ['verification_status' => $verificationData['verification_status'] ?? 'UNKNOWN'],
                    'Invalid PayPal webhook signature');

                return response()->json(['message' => 'Invalid signature'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Signature verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Signature verification failed'], 400);
        }

        // 2. Then validate the payload
        $rawPayload = $request->getContent();
        $data = json_decode($rawPayload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON payload received in webhook.', ['payload' => $rawPayload]);
            $this->notifyAnomaly('INVALID_JSON', null, null, null, null, ['raw_payload' => $rawPayload], 'Invalid JSON payload received');

            return response()->json(['message' => 'Invalid JSON payload'], 400);
        }

        // Extract and validate event type and resource
        $eventType = $data['event_type'] ?? null;
        $resource = $data['resource'] ?? null;

        if (! $eventType || ! $resource) {
            Log::warning('Event type or resource missing in webhook payload.', ['data' => $data]);
            $this->notifyAnomaly('MISSING_REQUIRED_FIELDS', null, null, null, null, ['data' => $data], 'Event type or resource missing in webhook payload');

            return response()->json(['message' => 'Incomplete webhook payload'], 400);
        }

        // 3. Validate order ID format before processing
        $orderId = $this->extractOrderId($eventType, $resource);
        if ($orderId !== null) {
            $this->trackOrderStatus($orderId, $eventType);

            // Log the order details before validation
            Log::info('Processing PayPal webhook for order', [
                'order_id' => $orderId,
                'event_type' => $eventType,
                'custom_id' => $resource['custom_id'] ?? null,
                'payment_id' => $resource['id'] ?? null,
            ]);

            if (! $this->isValidOrderId($orderId)) {
                Log::error('Invalid order ID format or range', [
                    'order_id' => $orderId,
                    'event_type' => $eventType,
                    'resource' => $resource,
                ]);

                // For production orders, add additional logging
                if (intval($orderId) >= 5000) {
                    Log::critical('Production order rejected', [
                        'order_id' => $orderId,
                        'event_type' => $eventType,
                        'resource' => $resource,
                    ]);
                }

                $this->notifyAnomaly('INVALID_ORDER_ID', $eventType, $resource, null, null,
                    ['order_id' => $orderId],
                    'Invalid order ID format or range');

                return response()->json(['message' => 'Invalid order ID'], 400);
            }
        }

        // Check for duplicate webhook event
        $transmissionId = $request->header('PAYPAL-TRANSMISSION-ID');
        if (PaypalWebhookEvent::where('transmission_id', $transmissionId)->exists()) {
            Log::info('Duplicate webhook event received.', ['transmission_id' => $transmissionId]);
            $this->notifyAnomaly('DUPLICATE_EVENT', $eventType, $resource, null, null, ['transmission_id' => $transmissionId], 'Duplicate webhook event received');

            return response()->json(['message' => 'Duplicate event'], 200);
        }

        // Find the corresponding payment record
        $payment = null;

        if ($orderId) {
            $payment = PaymentsPaypal::where('order_id', $orderId)->first();

            if (! $payment && in_array($eventType, ['CHECKOUT.ORDER.APPROVED', 'PAYMENT.CAPTURE.COMPLETED', 'PAYMENT.INSTALLMENT.APPROVED', 'PAYMENT.INSTALLMENT.COMPLETED', 'PAYMENT.INSTALLMENT.REFUNDED'])) {
                Log::error('Payment record not found for event', [
                    'order_id' => $orderId,
                    'event_type' => $eventType,
                ]);
                $this->notifyAnomaly('PAYMENT_RECORD_NOT_FOUND', $eventType, $resource, null, null, ['order_id' => $orderId], 'Payment record not found for event');
            }

            if ($payment && in_array($eventType, ['CHECKOUT.ORDER.APPROVED', 'PAYMENT.CAPTURE.COMPLETED', 'PAYMENT.INSTALLMENT.APPROVED', 'PAYMENT.INSTALLMENT.COMPLETED', 'PAYMENT.INSTALLMENT.REFUNDED'])) {
                $resource = $data['resource'];

                try {
                    // Update PaymentsPaypal record with null checks
                    $payment->update([
                        'transaction_id' => $resource['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                        'payer_email' => $resource['payer']['email_address'] ?? null,
                        'payer_name' => ($resource['payer']['name']['given_name'] ?? '').' '.($resource['payer']['name']['surname'] ?? ''),
                        'payer_id' => $resource['payer']['payer_id'] ?? null,
                        'paypal_fee' => $resource['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['paypal_fee']['value'] ?? null,
                        'net_amount' => $resource['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['net_amount']['value'] ?? null,
                        'status' => strtolower(str_replace('PAYMENT.', '', $eventType)),
                        'payment_response' => json_encode($resource),
                    ]);

                    // Update EcOrder status
                    $ecOrder = EcOrder::find($orderId);
                    if ($ecOrder) {
                        $ecOrder->order_status = 'In Progress';
                        $ecOrder->payment_status = 'paid';
                        $ecOrder->save();
                    } else {
                        Log::error('EcOrder not found for payment', [
                            'order_id' => $orderId,
                            'payment_id' => $payment->id,
                        ]);
                        $this->notifyAnomaly('ORDER_NOT_FOUND', $eventType, $resource, $payment, null, ['order_id' => $orderId], 'EcOrder not found for payment');
                    }

                    try {
                        $userId = DB::table('users')->where('employee_id', 'SYSTEM_ECOMMERCE')->value('id');
                    } catch (\Exception $e) {
                        Log::error('Error getting user ID', [
                            'error' => $e->getMessage(),
                        ]);
                    }

                    if ($userId && $ecOrder) {
                        $orderItems = $ecOrder->orderItems;

                        // Check if stock has already been deducted for this order
                        $stockMovementExists = NgnStockMovement::where('ref_doc_no', $ecOrder->id)
                            ->where('transaction_type', 'ECOMMERCE')
                            ->exists();

                        if (! $stockMovementExists) {
                            Log::info('Deducting stock for order', ['order_id' => $ecOrder->id]);
                            foreach ($orderItems as $item) {
                                try {
                                    NgnStockMovement::create([
                                        'branch_id' => $ecOrder->branch_id,
                                        'transaction_date' => now(),
                                        'product_id' => $item->product_id,
                                        'in' => 0,
                                        'out' => $item->quantity,
                                        'transaction_type' => 'ECOMMERCE',
                                        'user_id' => $userId,
                                        'ref_doc_no' => $ecOrder->id,
                                        'remarks' => 'Stock deducted for order approval',
                                    ]);
                                } catch (\Exception $e) {
                                    Log::error('Error creating stock movement', [
                                        'error' => $e->getMessage(),
                                        'product_id' => $item->product_id,
                                        'order_id' => $ecOrder->id,
                                    ]);
                                }

                                $product = NgnProduct::find($item->product_id);
                                if ($product) {
                                    $product->global_stock = $product->global_stock - $item->quantity;
                                    $product->save();
                                }
                            }
                        } else {
                            Log::info('Stock already deducted for order', ['order_id' => $ecOrder->id]);
                        }

                    }

                } catch (\Exception $e) {
                    Log::error('Error updating payment details', [
                        'error' => $e->getMessage(),
                        'order_id' => $orderId,
                    ]);
                    $this->notifyAnomaly('PAYMENT_UPDATE_ERROR', $eventType, $resource, $payment, null, ['error' => $e->getMessage()], 'Error updating payment details');
                }
            }
        }

        // Create webhook event record
        try {
            $webhookEvent = PaypalWebhookEvent::create([
                'payment_id' => $payment ? $payment->id : null,
                'event_type' => $eventType,
                'resource' => json_encode($resource),
                'payload' => $rawPayload,
                'transmission_id' => $transmissionId,
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url' => $request->header('PAYPAL-CERT-URL'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating webhook event record', [
                'error' => $e->getMessage(),
                'event_type' => $eventType,
            ]);
            $this->notifyAnomaly('WEBHOOK_RECORD_ERROR', $eventType, $resource, $payment, null, ['error' => $e->getMessage()], 'Error creating webhook event record');
        }

        // Update payment status regardless of verification status
        if ($payment) {
            try {
                $this->updatePaymentStatus($payment, $eventType);
            } catch (\Exception $e) {
                Log::error('Error updating payment status', [
                    'error' => $e->getMessage(),
                    'payment_id' => $payment->id,
                    'event_type' => $eventType,
                ]);
                $this->notifyAnomaly('STATUS_UPDATE_ERROR', $eventType, $resource, $payment, $webhookEvent, ['error' => $e->getMessage()], 'Error updating payment status');
            }
        }

        return response()->json(['message' => 'Webhook handled'], 200);
    }

    /**
     * Extract the Order ID from the webhook payload based on event type.
     */
    private function extractOrderId(string $eventType, array $resource): ?string
    {
        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':

                Log::info('CHECKOUT.ORDER.APPROVED:Resource:');

                return $resource['purchase_units'][0]['custom_id'] ?? null;

            case 'PAYMENT.CAPTURE.COMPLETED':
                Log::info('PAYMENT.CAPTURE.COMPLETED:Resource:');

                return $resource['custom_id'] ?? null;

            case 'PAYMENT.CAPTURE.PENDING':
                Log::info('PAYMENT.CAPTURE.PENDING:Resource:');

                return $resource['custom_id'] ?? null;

            case 'PAYMENT.CAPTURE.REFUNDED':
                if (isset($resource['custom_id'])) {
                    return $resource['custom_id'];
                }

                return $resource['supplementary_data']['related_ids']['order_id'] ?? null;

            case 'PAYMENT.CAPTURE.DENIED':

                Log::info('PAYMENT.CAPTURE.DENIED:Resource:');

            case 'PAYMENT.INSTALLMENT.APPROVED':
                Log::info('PAYMENT.INSTALLMENT.APPROVED:Resource:', ['resource' => $resource]);

                return $resource['custom_id'] ?? null;

            case 'PAYMENT.INSTALLMENT.COMPLETED':
                Log::info('PAYMENT.INSTALLMENT.COMPLETED:Resource:', ['resource' => $resource]);

                return $resource['custom_id'] ?? null;

            case 'PAYMENT.INSTALLMENT.REFUNDED':
                Log::info('PAYMENT.INSTALLMENT.REFUNDED:Resource:', ['resource' => $resource]);

                return $resource['custom_id'] ?? null;

            default:
                Log::info('Unhandled event type for Order ID extraction: '.$eventType);

                return null;
        }
    }

    /**
     * Update the payment status based on the event type.
     */
    private function updatePaymentStatus(PaymentsPaypal $payment, string $eventType): void
    {
        Log::info('updatePaymentStatus:Event Type:', ['event_type' => $eventType]);

        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
                $payment->status = 'approved';

                $ecOrder = EcOrder::find($payment->order_id);
                if ($ecOrder) {
                    $ecOrder->payment_reference = $payment->transaction_id;
                    $ecOrder->payment_date = $payment->update_time;
                    $ecOrder->save();

                    try {

                        if ($eventType === 'CHECKOUT.ORDER.APPROVED' && ! empty($payment->transaction_id)) {

                            \Log::info(' IF TRANSACTION ID AND ORDER ID IS NOT EMPTY CHECKOUT.ORDER.APPROVED ');
                            \Log::info($payment->transaction_id);
                            \Log::info($ecOrder->id);

                            Mail::to('enquiries@neguinhomotors.co.uk')
                                ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                                ->send(new NewOrderProcessMailer($ecOrder));

                        }

                    } catch (\Exception $e) {
                        Log::error('Order process email sending failed', [
                            'error' => $e->getMessage(),
                            'order_id' => $ecOrder->id,
                        ]);
                    }

                }
                break;

            case 'PAYMENT.CAPTURE.COMPLETED':
                Log::info('PAYMENT.CAPTURE.COMPLETED:Payment Response:');
                Log::info($payment->payment_response);

                $payment->status = 'completed';

                // Extract payment details from the resource
                $resource = json_decode($payment->payment_response, true);
                if (isset($resource['seller_receivable_breakdown'])) {
                    $breakdown = $resource['seller_receivable_breakdown'];
                    $paymentDetails = [
                        'transaction_id' => $resource['id'],
                        'status' => $resource['status'],
                        'paypal_fee' => $breakdown['paypal_fee']['value'] ?? null,
                        'net_amount' => $breakdown['net_amount']['value'] ?? null,
                        'gross_amount' => $breakdown['gross_amount']['value'] ?? null,
                        'create_time' => $resource['create_time'],
                        'update_time' => $resource['update_time'],
                    ];

                    // Update payment response with payment details
                    $paymentResponse = json_decode($payment->payment_response, true) ?? [];
                    $paymentResponse['payment_details'] = $paymentDetails;
                    $payment->payment_response = json_encode($paymentResponse);
                }

                Log::info('PAYMENT.CAPTURE.COMPLETED:Payment Response:');
                Log::info($payment->transaction_id);

                // Use order_id directly
                $ecOrder = EcOrder::find($payment->order_id);
                if ($ecOrder) {
                    $ecOrder->order_status = 'In Progress';
                    $ecOrder->payment_reference = $payment->transaction_id;
                    $ecOrder->payment_date = $payment->update_time;
                    $ecOrder->payment_status = 'paid';
                    $ecOrder->save();

                    session()->forget('cart');

                    $payment->status = 'completed';
                    $payment->save();

                    try {
                        try {
                            // Customer - Email
                            Mail::to($ecOrder->customer->email)
                                ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                                ->send(new OrderProcessMailer($ecOrder));

                        } catch (\Exception $e) {
                            Log::error('Order process email sending failed', [
                                'error' => $e->getMessage(),
                                'order_id' => $ecOrder->id,
                            ]);
                        }

                        try {
                            // Admin - Email
                            Mail::to('enquiries@neguinhomotors.co.uk')
                                ->bcc('support@neguinhomotors.co.uk')
                                ->send(new NewOrderProcessMailer($ecOrder));

                        } catch (\Exception $e) {
                            Log::error('Order process email sending failed', [
                                'error' => $e->getMessage(),
                                'order_id' => $ecOrder->id,
                            ]);
                        }

                        session()->forget('cart');

                        Log::info('Order process email sent successfully', [
                            'order_id' => $ecOrder->id,
                            'customer_email' => $ecOrder->customer->email,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Order process email sending failed', [
                            'error' => $e->getMessage(),
                            'order_id' => $ecOrder->id,
                        ]);
                    }
                }
                break;

            case 'PAYMENT.CAPTURE.REFUNDED':
                try {
                    $payment->status = 'refunded';

                    // Get the resource data from the payment response
                    $resource = json_decode($payment->payment_response, true);
                    if ($resource) {
                        $payment->payment_response = json_encode($resource);
                    }

                    $payment->save();

                    $ecOrder = EcOrder::with(['customer', 'customer.customer'])->find($payment->order_id);
                    if ($ecOrder) {
                        try {
                            if ($ecOrder->customer && $ecOrder->customer->email) {
                                Mail::to($ecOrder->customer->email)
                                    ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                                    ->send(new OrderRefundMailer($ecOrder));
                            } else {
                                Log::warning('Customer email not found for refund notification', [
                                    'order_id' => $ecOrder->id,
                                    'customer_id' => $ecOrder->customer_id,
                                ]);
                            }

                            // Update order statuses
                            $ecOrder->order_status = 'cancelled';
                            $ecOrder->payment_status = 'refunded';
                            $ecOrder->shipping_status = 'Cancelled';
                            $ecOrder->save();

                            Log::info('Refund processed successfully', [
                                'order_id' => $ecOrder->id,
                                'payment_id' => $payment->id,
                                'status' => 'refunded',
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error sending refund email', [
                                'error' => $e->getMessage(),
                                'order_id' => $ecOrder->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                            // Don't throw here - we still want to update the order status
                        }
                    } else {
                        Log::error('Order not found for refund', [
                            'order_id' => $payment->order_id,
                            'payment_id' => $payment->id,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing refund', [
                        'error' => $e->getMessage(),
                        'payment_id' => $payment->id,
                        'order_id' => $payment->order_id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw $e;
                }
                break;

            case 'PAYMENT.CAPTURE.DENIED':
                $payment->status = 'denied';
                $payment->save();
                break;

            case 'PAYMENT.CAPTURE.PENDING':
                $payment->status = 'pending';
                $payment->save();
                break;

            case 'PAYMENT.CAPTURE.REVERSED':
                $payment->status = 'reversed';
                $payment->save();
                break;

            case 'CHECKOUT.ORDER.CANCELLED':
            case 'PAYMENT.CAPTURE.CANCELLED':
                $payment->status = 'cancelled';
                $payment->save();
                break;

            case 'CHECKOUT.ORDER.VOIDED':
                $payment->status = 'voided';
                $payment->save();
                break;

            case 'PAYMENT.INSTALLMENT.APPROVED':
                $payment->status = 'installment_approved';
                $payment->save();
                Log::info('Payment installment approved', ['order_id' => $payment->order_id]);
                break;

            case 'PAYMENT.INSTALLMENT.COMPLETED':
                $payment->status = 'installment_completed';
                $payment->save();
                Log::info('Payment installment completed', ['order_id' => $payment->order_id]);
                break;

            case 'PAYMENT.INSTALLMENT.REFUNDED':
                $payment->status = 'installment_refunded';
                $payment->save();
                Log::info('Payment installment refunded', ['order_id' => $payment->order_id]);
                break;

            default:
                Log::info('Unhandled webhook event type:', ['event_type' => $eventType]);
                break;
        }

        Log::info('Payment status updated.', [
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'event_type' => $eventType,
        ]);
    }

    /**
     * Send anomaly notification email with safe variable handling
     */
    private function notifyAnomaly(
        string $anomalyType,
        ?string $eventType = null,
        ?array $resource = null,
        $payment = null,
        $webhookEvent = null,
        array $additionalData = [],
        ?string $errorMessage = null
    ) {
        try {
            // Ensure all variables are properly defined before using them
            $safeEventType = $eventType ?? 'UNKNOWN_EVENT';
            $safeResource = is_array($resource) ? $resource : [];
            $safePayment = $payment ?? null;
            $safeWebhookEvent = $webhookEvent ?? null;
            $safeAdditionalData = $additionalData ?? [];

            // Add debug information to additional data
            $safeAdditionalData['debug_info'] = [
                'timestamp' => now()->toIso8601String(),
                'variables_present' => [
                    'event_type' => ! is_null($eventType),
                    'resource' => ! is_null($resource),
                    'payment' => ! is_null($payment),
                    'webhook_event' => ! is_null($webhookEvent),
                ],
            ];

            // Send the new anomaly email
            Mail::to('admin@neguinhomotors.co.uk')
                ->bcc(['support@neguinhomotors.co.uk'])
                ->send(new PayPalWebhookAnomalyMailer(
                    $anomalyType,
                    $safeEventType,
                    $safeResource,
                    $safePayment,
                    $safeWebhookEvent,
                    $safeAdditionalData,
                    $errorMessage
                ));

            // If this is a signature verification issue, also send the original signature mailer
            // to maintain backward compatibility
            if ($anomalyType === 'INVALID_SIGNATURE' && $safeWebhookEvent) {
                try {
                    Mail::to('admin@neguinhomotors.co.uk')
                        ->bcc('support@neguinhomotors.co.uk')
                        ->send(new InvalidPayPalWebhookSignatureMailer($safeEventType, $safeResource, $safePayment, $safeWebhookEvent));
                } catch (\Exception $e) {
                    Log::error('Failed to send signature verification email', [
                        'error' => $e->getMessage(),
                        'anomaly_type' => $anomalyType,
                    ]);
                }
            }

            Log::info('Anomaly notification sent', [
                'type' => $anomalyType,
                'event_type' => $safeEventType,
                'has_webhook_event' => ! is_null($safeWebhookEvent),
                'has_payment' => ! is_null($safePayment),
                'error_message' => $errorMessage,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send anomaly notification email', [
                'error' => $e->getMessage(),
                'anomaly_type' => $anomalyType,
                'event_type' => $eventType ?? 'UNKNOWN',
                'trace' => $e->getTraceAsString(),
            ]);

            // Try to send a fallback notification for critical errors
            try {
                if (in_array($anomalyType, ['PROCESSING_ERROR', 'INVALID_SIGNATURE', 'ACCESS_TOKEN_ERROR'])) {
                    Mail::to('admin@neguinhomotors.co.uk')
                        ->send(new PayPalWebhookAnomalyMailer(
                            'NOTIFICATION_FAILURE',
                            null,
                            null,
                            null,
                            null,
                            [
                                'original_anomaly' => $anomalyType,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ],
                            'Failed to send original anomaly notification'
                        ));
                }
            } catch (\Exception $fallbackError) {
                Log::critical('Failed to send fallback anomaly notification', [
                    'error' => $fallbackError->getMessage(),
                    'original_anomaly' => $anomalyType,
                ]);
            }
        }
    }

    /**
     * Validate order ID format and existence
     */
    private function isValidOrderId($orderId): bool
    {
        try {
            // Log the validation attempt
            Log::info('Validating Order ID', [
                'order_id' => $orderId,
                'is_numeric' => is_numeric($orderId),
                'value' => intval($orderId),
            ]);

            // Basic format validation
            if (! is_numeric($orderId) || intval($orderId) < 5000) {
                Log::warning('Order ID format validation failed', [
                    'order_id' => $orderId,
                    'reason' => ! is_numeric($orderId) ? 'not numeric' : 'less than 5000',
                ]);

                return false;
            }

            // Check if order exists in database with more flexible status check
            $order = EcOrder::find($orderId);

            if (! $order) {
                Log::warning('Order not found in database', ['order_id' => $orderId]);

                return false;
            }

            // Log the order details for debugging
            Log::info('Order found in database', [
                'order_id' => $orderId,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
            ]);

            // More flexible status check
            $validStatuses = ['pending', 'processing', 'initiated', 'awaiting_payment'];

            // If the payment is already marked as paid, it might be a duplicate webhook
            // In this case, we should still consider it valid
            if ($order->payment_status === 'paid') {
                Log::info('Order already marked as paid', ['order_id' => $orderId]);

                return true;
            }

            $isValidStatus = in_array(strtolower($order->payment_status), $validStatuses);

            if (! $isValidStatus) {
                Log::warning('Invalid order payment status', [
                    'order_id' => $orderId,
                    'current_status' => $order->payment_status,
                    'valid_statuses' => $validStatuses,
                ]);
            }

            return $isValidStatus;

        } catch (\Exception $e) {
            Log::error('Error validating order ID', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // In case of error, we should accept the order to prevent losing payments
            return true;
        }
    }

    /**
     * Sanitize and validate resource data
     */
    private function sanitizeResource(array $resource): array
    {
        $sanitized = [];
        array_walk_recursive($resource, function ($value, $key) use (&$sanitized) {
            $sanitized[$key] = strip_tags($value);
        });

        return $sanitized;
    }

    private function getWebhookVerificationUrl(): string
    {
        $baseUrl = config('paypal.mode') === 'live'
            ? 'https://api.paypal.com'
            : 'https://api.sandbox.paypal.com';

        return $baseUrl.'/v1/notifications/verify-webhook-signature';
    }

    private function isDebugMode(): bool
    {
        return config('paypal.webhook_debug', false);
    }

    private function verifyConfiguration(): array
    {
        $config = config('paypal');
        $issues = [];

        // Check mode
        if ($config['mode'] !== 'live') {
            $issues[] = 'PayPal mode is not set to live';
            Log::error('PayPal Configuration Error: Mode is not live', ['current_mode' => $config['mode']]);
        }

        // Check webhook ID
        if (empty($config['webhook_id'])) {
            $issues[] = 'Webhook ID is missing';
            Log::error('PayPal Configuration Error: Missing webhook ID');
        }

        // Check client credentials
        if (empty($config['live']['client_id']) || empty($config['live']['client_secret'])) {
            $issues[] = 'Live credentials are missing';
            Log::error('PayPal Configuration Error: Missing live credentials');
        }

        return $issues;
    }

    private function verifyWebhookSignature(Request $request, array $data): array
    {
        // First verify configuration
        $configIssues = $this->verifyConfiguration();
        if (! empty($configIssues)) {
            Log::error('PayPal Configuration Issues', ['issues' => $configIssues]);

            return ['success' => false, 'errors' => $configIssues];
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));

            // Log the API mode and endpoint
            Log::info('PayPal API Configuration', [
                'mode' => config('paypal.mode'),
                'endpoint' => $this->getWebhookVerificationUrl(),
                'webhook_id' => config('paypal.webhook_id'),
            ]);

            $tokenResponse = $provider->getAccessToken();

            if (! isset($tokenResponse['access_token'])) {
                throw new \Exception('Failed to retrieve access token');
            }

            // Create verification payload
            $verificationPayload = [
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url' => $request->header('PAYPAL-CERT-URL'),
                'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'webhook_id' => config('paypal.webhook_id'),
                'webhook_event' => $data,
            ];

            // Log verification attempt
            Log::info('PayPal Verification Attempt', [
                'headers_present' => [
                    'auth_algo' => ! empty($request->header('PAYPAL-AUTH-ALGO')),
                    'cert_url' => ! empty($request->header('PAYPAL-CERT-URL')),
                    'transmission_id' => ! empty($request->header('PAYPAL-TRANSMISSION-ID')),
                    'transmission_sig' => ! empty($request->header('PAYPAL-TRANSMISSION-SIG')),
                    'transmission_time' => ! empty($request->header('PAYPAL-TRANSMISSION-TIME')),
                ],
                'webhook_id' => config('paypal.webhook_id'),
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$tokenResponse['access_token'],
            ])->post($this->getWebhookVerificationUrl(), $verificationPayload);

            $verificationData = $response->json();

            // Log verification response
            Log::info('PayPal Verification Response', [
                'status' => $response->status(),
                'verification_status' => $verificationData['verification_status'] ?? 'UNKNOWN',
            ]);

            return [
                'success' => $response->successful() &&
                            ($verificationData['verification_status'] ?? '') === 'SUCCESS',
                'data' => $verificationData,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal Verification Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function trackOrderStatus($orderId, $eventType): void
    {
        try {
            Log::info('PayPal Webhook Order Status', [
                'order_id' => $orderId,
                'event_type' => $eventType,
                'timestamp' => now()->toIso8601String(),
            ]);

            // Find the payment record for this order
            $payment = PaymentsPaypal::where('order_id', $orderId)->first();

            // Record the webhook event status using PaypalWebhookEvent model
            PaypalWebhookEvent::create([
                'payment_id' => $payment ? $payment->id : null,
                'event_type' => $eventType,
                'resource' => json_encode(['order_id' => $orderId]),
                'transmission_time' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track order status', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
