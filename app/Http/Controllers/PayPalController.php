<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Ecommerce\EcOrder;
use App\Models\PaymentsPaypal;
use App\Models\PaypalWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    // Modified directPayment method
    public function directPayment(Request $request)
    {
        Log::info('directPayment:Direct Payment Request:', $request->all());

        // Get authenticated customer
        $customer = auth('customer')->user();
        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        // Get return URL from request or default to a specific route
        $returnUrl = route('paypal.success');

        // Get pending order with items
        $pendingOrder = EcOrder::with(['orderItems.product', 'shippingMethod'])
            ->where('customer_id', $customer->id)
            ->where('order_status', 'pending')
            ->where('payment_status', 'pending')
            ->first();

        if (! $pendingOrder) {
            return redirect($returnUrl.'?payment_status=error&message='.urlencode('No pending order found.'));
        }

        // Get shipping method or stopped payment
        $shippingMethod = $pendingOrder->shippingMethod;
        if (! $shippingMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a shipping method first.',
            ], 400);
        }

        // Set shipping details based on method
        if ($shippingMethod->in_store_pickup) {
            // For store pickup, get branch details
            if (! $pendingOrder->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not selected for store pickup.',
                ], 400);
            }

            // branch need to be found
            $branch = Branch::find($pendingOrder->branch_id);
            if (! $branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected branch not found.',
                ], 404);
            }

            // PayPal needs this format
            $shippingDetails = [
                'line1' => $branch->address,
                'city' => $branch->city,
                'postal_code' => $branch->postal_code,
                'country_code' => 'GB',
            ];
        } else {
            // For home delivery, use customer's address
            // from CustomerAddress model customer_addresses.id from ec_orders.customer_address_id
            // Until Shipping method is In Store Pickup, it has to be remain like this
            $shippingDetails = [
                'line1' => $customer->customer->address,
                'line2' => $customer->customer->address2,
                'city' => $customer->customer->city,
                'postal_code' => $customer->customer->postal_code,
                'country_code' => 'GB',
            ];
        }

        // Use the actual pending order ID
        $orderId = (string) $pendingOrder->id;

        // Create a payment record with shipping details (create a new record, on each try)
        $payment = PaymentsPaypal::create([
            'customer_id' => $customer->id,
            'order_id' => $orderId,
            'amount' => $pendingOrder->total_amount + $pendingOrder->shipping_cost - $pendingOrder->discount,
            'currency' => 'GBP',
            'status' => 'pending',
            'payment_response' => json_encode([
                'shipping_method' => $shippingMethod->in_store_pickup ? 'collection' : 'delivery',
                'shipping_details' => $shippingDetails,
            ]),
        ]);

        // Initialize PayPal
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $accessToken = $provider->getAccessToken();

            if (! is_array($accessToken)) {
                Log::error('PayPal Error: Invalid access token format', [
                    'token_response' => $accessToken,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment gateway',
                ], 500);
            }

            if (empty($accessToken['access_token'])) {
                Log::error('PayPal Error: Access token not found in response', [
                    'response' => $accessToken,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment gateway',
                ], 500);
            }

            $provider->setAccessToken($accessToken);
        } catch (\Exception $e) {
            Log::error('PayPal Authentication Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment gateway authentication failed',
            ], 500);
        }

        // Format order items for PayPal
        $paypalItems = $pendingOrder->orderItems->map(function ($item) {
            // Objectivy all items to be sent to PayPal
            return [
                'name' => $item->product_name,
                'description' => $item->sku ? "SKU: {$item->sku}" : null,
                'sku' => $item->product_id,  // Using product_id as item number
                'quantity' => (string) $item->quantity,
                'unit_amount' => [
                    'currency_code' => 'GBP',
                    'value' => number_format($item->unit_price, 2, '.', ''),
                ],
                'category' => 'PHYSICAL_GOODS',
            ];
        })->toArray();

        // Calculate totals
        $itemTotal = $pendingOrder->orderItems->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        // Instead, update the total_amount to include shipping
        $pendingOrder->total_amount = $itemTotal;
        $pendingOrder->save();

        $shippingInfo = [
            'name' => [
                'full_name' => $customer->customer->getFullNameAttribute(),
            ],
            'address' => [
                'address_line_1' => $shippingDetails['line1'],
                'address_line_2' => $shippingDetails['line2'] ?? '',
                'admin_area_2' => $shippingDetails['city'],
                'postal_code' => $shippingDetails['postal_code'],
                'country_code' => strtoupper($shippingDetails['country_code']),
            ],
        ];

        Log::info('directPayment:Shipping Details:', [$returnUrl, $shippingDetails]);

        $data = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => $returnUrl,
                'cancel_url' => route('paypal.cancel'),
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'payment_method' => [
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                ],
            ],
            'purchase_units' => [
                [
                    'reference_id' => 'default',
                    'amount' => [
                        'currency_code' => 'GBP',
                        // total_amount = itemTotal + shipping_cost (tax included in unit_price)
                        'value' => number_format($pendingOrder->grand_total, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'GBP',
                                // itemTotal = sum of (unit_price * quantity) for all items
                                'value' => number_format($itemTotal, 2, '.', ''),
                            ],
                            'shipping' => [
                                'currency_code' => 'GBP',
                                'value' => number_format($pendingOrder->shipping_cost, 2, '.', ''),
                            ],
                            'tax_total' => [
                                'currency_code' => 'GBP',
                                'value' => '0.00',  // tax is already included in unit_price
                            ],
                        ],
                    ],
                    'custom_id' => $orderId,  // from EcOrder->id
                    'description' => 'Order Reference: '.$orderId,
                    'items' => $paypalItems,
                    'shipping' => $shippingInfo,
                    'custom' => json_encode([
                        'order_id' => $orderId,
                        'customer_id' => $customer->id,  // This is customer_auth.id
                        'shipping_method' => $shippingMethod->in_store_pickup ? 'collection' : 'delivery',
                    ]),
                ],
            ],
        ];

        Log::info('directPayment:PayPal Order Payload:', $data);

        try {
            // Create the order in PayPal
            $order = $provider->createOrder($data);

            // Ensure 'links' key exists and is an array
            if (! isset($order['links']) || ! is_array($order['links'])) {
                Log::error('PayPal Error: "links" key missing in PayPal response.', ['order_response' => $order]);

                return redirect($returnUrl.'?payment_status=error&message='.urlencode('Invalid PayPal response.'));
            }

            // Find the approval link to redirect the user
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }

            // If 'approve' link is not found
            Log::error('PayPal Error: Approval link not found in the order response.', ['order_response' => $order]);

            return redirect($returnUrl.'?payment_status=error&message='.urlencode('Approval link not found in PayPal response.'));

        } catch (\Exception $e) {

            Log::error('directPayment:PayPal Exception: '.$e->getMessage());

            return redirect($returnUrl.'?payment_status=error&message='.urlencode($e->getMessage()));
        }
    }

    // Modified success method
    public function success(Request $request)
    {
        try {
            // Validate token presence
            if (! $request->token) {
                Log::error('PayPal token missing in request');

                return redirect('/shop/checkout')
                    ->with([
                        'payment_status' => 'error',
                        'message' => 'Invalid payment token',
                    ]);
            }

            // Initialize PayPal
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            // Capture the payment
            $response = $provider->capturePaymentOrder($request->token);

            // Log the raw response for debugging
            Log::info('PayPal Raw Response:', $response);

            // Ensure the response is an array
            if (is_object($response)) {
                $response = json_decode(json_encode($response), true);
            }

            // Extract the custom_id (order_id) from the captures
            $orderId = $response['purchase_units'][0]['payments']['captures'][0]['custom_id'] ?? null;
            $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            if (! $orderId || ! $captureId) {
                Log::error('Missing required fields in PayPal response', [
                    'order_id' => $orderId,
                    'capture_id' => $captureId,
                    'response' => $response,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid PayPal response: Missing required fields',
                ], 400);
            }

            // Extract payment details
            $paymentDetails = [
                'transaction_id' => $captureId,
                'status' => $response['status'] ?? 'unknown',
                'payer_email' => $response['payer']['email_address'] ?? null,
                'payer_name' => ($response['payer']['name']['given_name'] ?? '').' '.($response['payer']['name']['surname'] ?? ''),
                'payer_id' => $response['payer']['payer_id'] ?? null,
                'payment_response' => json_encode($response),
            ];

            // Extract financial details
            if (isset($response['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown'])) {
                $breakdown = $response['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown'];
                $paymentDetails['paypal_fee'] = $breakdown['paypal_fee']['value'] ?? null;
                $paymentDetails['net_amount'] = $breakdown['net_amount']['value'] ?? null;
            }

            // Update payment record
            if ($response['status'] === 'COMPLETED') {
                $payment = PaymentsPaypal::where('order_id', $orderId)->first();

                if (! $payment) {
                    Log::error('Payment record not found', ['order_id' => $orderId]);

                    return redirect('/shop/checkout')
                        ->with([
                            'payment_status' => 'error',
                            'message' => 'Payment record not found',
                        ]);
                }

                // Decode existing payment_response to append additional details
                $paymentResponse = json_decode($payment->payment_response, true);
                $paymentResponse['payment_details'] = $paymentDetails;

                // Update payment record with all details
                $payment->payment_response = json_encode($paymentResponse);
                $payment->status = 'completed';
                $payment->save();

                // Update order status
                $order = EcOrder::find($orderId);
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->order_status = 'In Progress';
                    $order->save();
                }

                // Clear specific session data first
                $request->session()->forget([
                    'cart',
                    'checkout_state',
                    'shipping_details',
                    'previous_product',
                    'cart_items',
                ]);

                // Redirect back to checkout with success status
                return redirect('/shop/checkout')
                    ->with([
                        'payment_status' => 'success',
                        'transaction_id' => $captureId,
                    ]);
            }

            // Handle non-completed status
            return redirect('/shop/checkout')
                ->with([
                    'payment_status' => 'error',
                    'message' => 'Payment not completed',
                ]);

        } catch (\Exception $e) {
            Log::error('PayPal Success Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/shop/checkout')
                ->with([
                    'payment_status' => 'error',
                    'message' => 'An error occurred while processing the payment',
                ]);
        }
    }

    // Modified cancel method
    public function cancel(Request $request)
    {
        Log::info('cancel:Payment Cancelled:', $request->all());

        // Find the payment record using the token
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);

        try {
            // Get order details from PayPal
            $order = $provider->showOrderDetails($request->token);

            // Ensure the response is an array
            if (is_object($order)) {
                $order = json_decode(json_encode($order), true);
            }

            // Extract custom_id (our order_id) from the order details
            $orderId = $order['purchase_units'][0]['custom_id'] ?? null;

            if ($orderId) {
                // Update payment status in our database
                $payment = PaymentsPaypal::where('order_id', $orderId)->first();
                if ($payment) {
                    $payment->status = 'cancelled';
                    $payment->save();

                    // Create a manual webhook event record
                    PaypalWebhookEvent::create([
                        'payment_id' => $payment->id,
                        'event_type' => 'CHECKOUT.ORDER.CANCELLED',
                        'resource' => json_encode($order),
                        'payload' => json_encode([
                            'event_type' => 'CHECKOUT.ORDER.CANCELLED',
                            'resource' => $order,
                        ]),
                        'transmission_id' => 'MANUAL_'.uniqid(),
                        'transmission_time' => now()->toIso8601String(),
                        'transmission_sig' => 'MANUAL',
                        'auth_algo' => 'MANUAL',
                        'cert_url' => 'MANUAL',
                    ]);

                    Log::info('Payment Cancelled', [
                        'order_id' => $orderId,
                        'shipping_method' => json_decode($payment->payment_response, true)['shipping_method'] ?? 'unknown',
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error processing cancellation:', [
                'token' => $request->token,
                'error' => $e->getMessage(),
            ]);
        }

        // Redirect back to checkout with cancel status
        return redirect('/shop/checkout')
            ->with([
                'payment_status' => 'cancelled',
                'message' => 'You have canceled the transaction.',
            ]);
    }
}
