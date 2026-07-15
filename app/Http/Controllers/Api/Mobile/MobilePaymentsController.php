<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BookingInvoice;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use App\Models\Ecommerce\EcOrder;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayOnboarding;
use App\Models\JudopaySubscription;
use App\Models\PaymentsPaypal;
use App\Models\RentingBooking;
use App\Services\JudopayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

/**
 * Native-app payment sessions that mirror the web PayPal (shop) and
 * Judopay (rental) flows, returning JSON instead of redirects.
 */
class MobilePaymentsController extends Controller
{
    public function paypalCreateOrder(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'order_id' => ['required', 'integer', 'exists:ec_orders,id'],
        ]);

        $order = EcOrder::with(['items.product', 'shippingMethod'])
            ->where('customer_id', $customer->id)
            ->find((int) $payload['order_id']);

        if (! $order) {
            return response()->json(['message' => 'Order not found for this customer.'], 404);
        }

        if ($order->items->isEmpty()) {
            return response()->json(['message' => 'Order has no items.'], 422);
        }

        $shippingMethod = $order->shippingMethod;
        if (! $shippingMethod) {
            return response()->json(['message' => 'Select a shipping method before paying.'], 422);
        }

        if ($shippingMethod->in_store_pickup) {
            if (! $order->branch_id) {
                return response()->json(['message' => 'Branch not selected for store pickup.'], 422);
            }

            $branch = Branch::find($order->branch_id);
            if (! $branch) {
                return response()->json(['message' => 'Selected branch not found.'], 404);
            }

            $shippingDetails = [
                'line1' => $branch->address,
                'line2' => '',
                'city' => $branch->city,
                'postal_code' => $branch->postal_code,
                'country_code' => 'GB',
            ];
        } else {
            $deliveryAddress = $order->customer_address_id
                ? CustomerAddress::find($order->customer_address_id)
                : null;

            $shippingDetails = [
                'line1' => $deliveryAddress->street_address ?? $customer->customer->address ?? '',
                'line2' => $deliveryAddress->street_address_plus ?? ($customer->customer->address2 ?? ''),
                'city' => $deliveryAddress->city ?? $customer->customer->city ?? '',
                'postal_code' => $deliveryAddress->postcode ?? $customer->customer->postal_code ?? '',
                'country_code' => 'GB',
            ];
        }

        $itemTotal = round((float) $order->items->sum(fn ($item) => (float) $item->unit_price * (int) $item->quantity), 2);
        $order->total_amount = $itemTotal;
        $order->save();

        $payment = PaymentsPaypal::create([
            'customer_id' => $customer->id,
            'order_id' => (string) $order->id,
            'amount' => $itemTotal + (float) $order->shipping_cost - (float) $order->discount,
            'currency' => 'GBP',
            'status' => 'pending',
            'payment_response' => json_encode([
                'source' => 'mobile',
                'shipping_method' => $shippingMethod->in_store_pickup ? 'collection' : 'delivery',
                'shipping_details' => $shippingDetails,
            ]),
        ]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $accessToken = $provider->getAccessToken();

            if (! is_array($accessToken) || empty($accessToken['access_token'])) {
                Log::error('Mobile PayPal: invalid access token', ['response' => $accessToken]);

                return response()->json(['message' => 'Failed to initialise payment gateway.'], 502);
            }

            $provider->setAccessToken($accessToken);
        } catch (\Throwable $e) {
            Log::error('Mobile PayPal auth error: '.$e->getMessage());

            return response()->json(['message' => 'Payment gateway authentication failed.'], 502);
        }

        $paypalItems = $order->items->map(fn ($item) => [
            'name' => $item->product_name ?: ($item->product?->name ?: 'Item'),
            'sku' => (string) $item->product_id,
            'quantity' => (string) $item->quantity,
            'unit_amount' => [
                'currency_code' => 'GBP',
                'value' => number_format((float) $item->unit_price, 2, '.', ''),
            ],
            'category' => 'PHYSICAL_GOODS',
        ])->toArray();

        $grandTotal = round($itemTotal + (float) $order->shipping_cost - (float) $order->discount, 2);

        $data = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('mobile.paypal.return'),
                'cancel_url' => route('mobile.paypal.return'),
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'payment_method' => ['payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'],
            ],
            'purchase_units' => [[
                'reference_id' => 'default',
                'amount' => [
                    'currency_code' => 'GBP',
                    'value' => number_format($grandTotal, 2, '.', ''),
                    'breakdown' => [
                        'item_total' => ['currency_code' => 'GBP', 'value' => number_format($itemTotal, 2, '.', '')],
                        'shipping' => ['currency_code' => 'GBP', 'value' => number_format((float) $order->shipping_cost, 2, '.', '')],
                        'tax_total' => ['currency_code' => 'GBP', 'value' => '0.00'],
                    ],
                ],
                'custom_id' => (string) $order->id,
                'description' => 'Order Reference: '.$order->id,
                'items' => $paypalItems,
                'shipping' => [
                    'name' => ['full_name' => $customer->customer?->getFullNameAttribute() ?? ''],
                    'address' => [
                        'address_line_1' => $shippingDetails['line1'],
                        'address_line_2' => $shippingDetails['line2'],
                        'admin_area_2' => $shippingDetails['city'],
                        'postal_code' => $shippingDetails['postal_code'],
                        'country_code' => $shippingDetails['country_code'],
                    ],
                ],
                'custom' => json_encode([
                    'order_id' => (string) $order->id,
                    'customer_id' => $customer->id,
                    'shipping_method' => $shippingMethod->in_store_pickup ? 'collection' : 'delivery',
                    'source' => 'mobile',
                ]),
            ]],
        ];

        try {
            $paypalOrder = $provider->createOrder($data);

            if (! isset($paypalOrder['links']) || ! is_array($paypalOrder['links'])) {
                Log::error('Mobile PayPal: missing links in order response', ['order' => $paypalOrder]);

                return response()->json(['message' => 'Invalid PayPal response.'], 502);
            }

            $approvalUrl = null;
            foreach ($paypalOrder['links'] as $link) {
                if (($link['rel'] ?? null) === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if (! $approvalUrl) {
                Log::error('Mobile PayPal: approve link not found', ['order' => $paypalOrder]);

                return response()->json(['message' => 'Approval link not found in PayPal response.'], 502);
            }

            return response()->json([
                'data' => [
                    'payment_id' => $payment->id,
                    'paypal_order_id' => $paypalOrder['id'] ?? null,
                    'approval_url' => $approvalUrl,
                    'amount' => $grandTotal,
                    'currency' => 'GBP',
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Mobile PayPal create order exception: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 502);
        }
    }

    /**
     * Called by the app after the customer approves payment in the PayPal
     * WebView/browser and returns with a `token` (PayPal order id).
     */
    public function paypalCaptureOrder(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'token' => ['required', 'string'],
        ]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setAccessToken($provider->getAccessToken());

            $response = $provider->capturePaymentOrder($payload['token']);
            if (is_object($response)) {
                $response = json_decode(json_encode($response), true);
            }

            $orderId = $response['purchase_units'][0]['payments']['captures'][0]['custom_id'] ?? null;
            $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            if (! $orderId || ! $captureId) {
                Log::error('Mobile PayPal capture: missing fields', ['response' => $response]);

                return response()->json(['message' => 'Invalid PayPal response: missing capture fields.'], 422);
            }

            $order = EcOrder::where('customer_id', $customer->id)->find((int) $orderId);
            if (! $order) {
                return response()->json(['message' => 'Order not found for this customer.'], 404);
            }

            $payment = PaymentsPaypal::where('order_id', (string) $orderId)
                ->where('customer_id', $customer->id)
                ->latest('id')
                ->first();

            $status = $response['status'] ?? 'unknown';

            if ($payment) {
                $existing = json_decode((string) $payment->payment_response, true) ?: [];
                $existing['payment_details'] = [
                    'transaction_id' => $captureId,
                    'status' => $status,
                    'payer_email' => $response['payer']['email_address'] ?? null,
                    'payer_id' => $response['payer']['payer_id'] ?? null,
                ];
                $payment->payment_response = json_encode($existing);
                $payment->transaction_id = $captureId;
                $payment->status = $status === 'COMPLETED' ? 'completed' : 'failed';
                $payment->save();
            }

            if ($status !== 'COMPLETED') {
                return response()->json([
                    'message' => 'Payment was not completed.',
                    'data' => ['status' => $status],
                ], 422);
            }

            $order->payment_status = 'paid';
            $order->order_status = 'In Progress';
            $order->save();

            return response()->json([
                'message' => 'Payment captured successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'transaction_id' => $captureId,
                    'status' => $status,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Mobile PayPal capture exception: '.$e->getMessage());

            return response()->json(['message' => 'Unable to capture PayPal payment.'], 502);
        }
    }

    /**
     * Bridge page for the PayPal `return_url`/`cancel_url` when opened inside
     * an in-app browser/WebView. The app should watch for this path and pull
     * the `token` query parameter, then call paypalCaptureOrder.
     */
    public function paypalReturnBridge(Request $request)
    {
        return response()->json([
            'message' => 'Return to the app to complete payment.',
            'data' => [
                'token' => $request->query('token'),
                'paypal_order_id' => $request->query('token'),
            ],
        ]);
    }

    /**
     * Create a Judopay CIT payment session for a rental booking invoice,
     * mirroring Portal\RentalPaymentController::initializePayment but
     * returning JSON for the app instead of a redirect.
     */
    public function judopayRentalSession(Request $request, int $bookingId): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $profile = $customer->customer;
        if (! $profile) {
            return response()->json(['message' => 'Customer profile is missing. Please contact support.'], 422);
        }

        $booking = RentingBooking::with(['customer', 'rentingBookingItems.motorbike'])->find($bookingId);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if ($booking->customer_id !== $profile->id) {
            return response()->json(['message' => 'Unauthorized access to booking.'], 403);
        }

        $invoice = BookingInvoice::where('booking_id', $bookingId)->where('is_paid', false)->first();
        if (! $invoice) {
            return response()->json(['message' => 'No pending invoice found for this booking.'], 422);
        }

        $consumerReference = 'CUST-'.$profile->id.'-'.time();

        $subscription = JudopaySubscription::firstOrCreate(
            ['subscribable_id' => $booking->id, 'subscribable_type' => RentingBooking::class],
            [
                'consumer_reference' => $consumerReference,
                'status' => 'pending',
                'payment_frequency' => 'weekly',
                'amount' => $invoice->amount - $invoice->deposit,
            ]
        );

        JudopayOnboarding::firstOrCreate(
            ['onboardable_id' => $profile->id, 'onboardable_type' => get_class($profile)],
            ['consumer_reference' => $consumerReference, 'onboarding_status' => 'pending']
        );

        $payload = [
            'amount' => (float) $invoice->amount,
            'currency' => config('judopay.currency', 'GBP'),
            'yourConsumerReference' => $consumerReference,
            'yourPaymentReference' => JudopayService::generatePaymentReference('cit', $consumerReference),
            'judoId' => config('judopay.judo_id'),
            'customerDetails' => ['yourConsumerReference' => $consumerReference],
            'metadata' => [
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id,
                'customer_id' => $profile->id,
                'type' => 'rental_booking_payment',
                'source' => 'mobile',
            ],
        ];

        try {
            $response = Http::withHeaders(JudopayService::getHeaders())
                ->timeout(config('judopay.timeout', 30))
                ->post(JudopayService::getApiUrl(config('judopay.endpoints.webpayments')), $payload);
        } catch (\Throwable $e) {
            Log::error('Mobile Judopay CIT request exception', ['booking_id' => $bookingId, 'error' => $e->getMessage()]);

            return response()->json(['message' => 'Payment initialisation failed. Please try again.'], 502);
        }

        if (! $response->successful()) {
            Log::error('Mobile Judopay CIT creation failed', ['booking_id' => $bookingId, 'response' => $response->json()]);

            return response()->json(['message' => 'Payment initialisation failed. Please try again.'], 502);
        }

        $responseData = $response->json();

        $citSession = JudopayCitPaymentSession::create([
            'subscription_id' => $subscription->id,
            'judopay_reference' => $responseData['reference'] ?? null,
            'judopay_payment_reference' => $payload['yourPaymentReference'],
            'amount' => $payload['amount'],
            'currency' => $payload['currency'],
            'status' => 'created',
            'is_active' => true,
            'postUrl' => $responseData['postUrl'] ?? null,
            'reference' => $responseData['reference'] ?? null,
        ]);

        $paymentUrl = $responseData['redirectUrl'] ?? $responseData['postUrl'] ?? null;
        if (! $paymentUrl) {
            return response()->json(['message' => 'Payment URL not received from Judopay.'], 502);
        }

        return response()->json([
            'data' => [
                'session_id' => $citSession->id,
                'payment_url' => $paymentUrl,
                'reference' => $responseData['reference'] ?? null,
                'amount' => (float) $invoice->amount,
                'currency' => $payload['currency'],
            ],
        ], 201);
    }

    /**
     * Called by the app after Judopay's `success` return so the booking and
     * invoice reflect the completed payment (mirrors RentalPaymentController::success).
     */
    public function judopayRentalStatus(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'reference' => ['required', 'string'],
            'receipt_id' => ['nullable', 'string'],
            'card_token' => ['nullable', 'string'],
        ]);

        $citSession = JudopayCitPaymentSession::where('reference', $payload['reference'])
            ->orWhere('judopay_reference', $payload['reference'])
            ->first();

        if (! $citSession) {
            return response()->json(['message' => 'Payment session not found.'], 404);
        }

        $subscription = $citSession->subscription;
        $booking = $subscription?->subscribable;

        if (! $booking instanceof RentingBooking || $booking->customer_id !== $customer->customer?->id) {
            return response()->json(['message' => 'Unauthorized access to this payment session.'], 403);
        }

        $citSession->update([
            'status' => 'success',
            'judopay_receipt_id' => $payload['receipt_id'] ?? null,
            'payment_completed_at' => now(),
        ]);

        $subscription->update([
            'card_token' => $payload['card_token'] ?? $subscription->card_token,
            'receipt_id' => $payload['receipt_id'] ?? $subscription->receipt_id,
            'status' => 'active',
        ]);

        $invoice = BookingInvoice::where('booking_id', $booking->id)->where('is_paid', false)->first();
        if ($invoice) {
            $invoice->update(['is_paid' => true, 'paid_date' => now(), 'is_posted' => true, 'state' => 'Paid']);
        }

        $booking->update(['state' => 'Awaiting Documents', 'is_posted' => true]);

        return response()->json([
            'message' => 'Payment confirmed. Rental booking is now active.',
            'data' => ['booking_id' => $booking->id, 'state' => $booking->state],
        ]);
    }

    private function customer(Request $request): ?CustomerAuth
    {
        $actor = $request->user('customer') ?: $request->user('sanctum');

        return $actor instanceof CustomerAuth ? $actor : null;
    }
}
