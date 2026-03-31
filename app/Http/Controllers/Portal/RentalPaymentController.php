<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BookingInvoice;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayOnboarding;
use App\Models\JudopaySubscription;
use App\Models\RentingBooking;
use App\Services\JudopayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RentalPaymentController extends Controller
{
    /**
     * Initialize Judopay CIT session for rental booking payment
     */
    public function initializePayment(Request $request, $bookingId)
    {
        try {
            $booking = RentingBooking::with(['customer', 'rentingBookingItems.motorbike'])->findOrFail($bookingId);
            $customer = Auth::guard('customer')->user()->customer;

            if (! $customer) {
                return redirect()->route('account.rentals')->with('error', 'Customer profile is missing. Please contact support.');
            }

            // Verify this booking belongs to logged-in customer
            if ($booking->customer_id !== $customer->id) {
                return redirect()->route('account.rentals')->with('error', 'Unauthorized access to booking.');
            }

            // Get unpaid invoice
            $invoice = BookingInvoice::where('booking_id', $bookingId)
                ->where('is_paid', false)
                ->first();

            if (! $invoice) {
                return redirect()->route('account.rentals')->with('error', 'No pending invoice found.');
            }

            // Generate unique consumer reference
            $consumerReference = 'CUST-'.$customer->id.'-'.time();

            // Create or get Judopay subscription
            $subscription = JudopaySubscription::firstOrCreate(
                ['subscribable_id' => $booking->id, 'subscribable_type' => RentingBooking::class],
                [
                    'consumer_reference' => $consumerReference,
                    'status' => 'pending',
                    'payment_frequency' => 'weekly',
                    'amount' => $invoice->amount - $invoice->deposit, // Weekly rent amount
                ]
            );

            // Create Judopay onboarding if not exists
            JudopayOnboarding::firstOrCreate(
                ['onboardable_id' => $customer->id, 'onboardable_type' => get_class($customer)],
                [
                    'consumer_reference' => $consumerReference,
                    'onboarding_status' => 'pending',
                ]
            );

            // Prepare payment payload
            $payload = [
                'amount' => (float) $invoice->amount,
                'currency' => config('judopay.currency', 'GBP'),
                'yourConsumerReference' => $consumerReference,
                'yourPaymentReference' => JudopayService::generatePaymentReference('cit', $consumerReference),
                'judoId' => config('judopay.judo_id'),
                'customerDetails' => [
                    'yourConsumerReference' => $consumerReference,
                ],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'invoice_id' => $invoice->id,
                    'customer_id' => $customer->id,
                    'type' => 'rental_booking_payment',
                ],
            ];

            // Create CIT payment session
            $response = \Illuminate\Support\Facades\Http::withHeaders(JudopayService::getHeaders())
                ->timeout(config('judopay.timeout', 30))
                ->post(JudopayService::getApiUrl(config('judopay.endpoints.webpayments')), $payload);

            if (! $response->successful()) {
                Log::error('Judopay CIT creation failed', [
                    'booking_id' => $bookingId,
                    'response' => $response->json(),
                ]);

                return redirect()->back()->with('error', 'Payment initialization failed. Please try again.');
            }

            $responseData = $response->json();

            // Store CIT session
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

            // Redirect to Judopay payment page
            if (isset($responseData['redirectUrl'])) {
                return redirect($responseData['redirectUrl']);
            } elseif (isset($responseData['postUrl'])) {
                return redirect($responseData['postUrl']);
            }

            return redirect()->back()->with('error', 'Payment URL not received from Judopay.');

        } catch (\Exception $e) {
            Log::error('Rental payment initialization failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Payment initialization failed: '.$e->getMessage());
        }
    }

    /**
     * Handle Judopay success callback
     */
    public function success(Request $request)
    {
        Log::info('Judopay payment success callback', $request->all());

        $receiptId = $request->input('ReceiptId');
        $reference = $request->input('Reference');
        $cardToken = $request->input('CardToken');

        try {
            // Find CIT session
            $citSession = JudopayCitPaymentSession::where('reference', $reference)
                ->orWhere('judopay_reference', $reference)
                ->first();

            if (! $citSession) {
                Log::warning('CIT session not found for success callback', ['reference' => $reference]);

                return redirect()->route('account.rentals')->with('warning', 'Payment received but session not found. Please contact support.');
            }

            // Update CIT session
            $citSession->update([
                'status' => 'success',
                'judopay_receipt_id' => $receiptId,
                'payment_completed_at' => now(),
            ]);

            // Update subscription
            $subscription = $citSession->subscription;
            $subscription->update([
                'card_token' => $cardToken,
                'receipt_id' => $receiptId,
                'status' => 'active',
            ]);

            // Get booking and mark invoice as paid
            $booking = $subscription->subscribable;
            $invoice = BookingInvoice::where('booking_id', $booking->id)
                ->where('is_paid', false)
                ->first();

            if ($invoice) {
                $invoice->update([
                    'is_paid' => true,
                    'paid_date' => now(),
                    'is_posted' => true,
                    'state' => 'Paid',
                ]);
            }

            // Update booking state
            $booking->update([
                'state' => 'Awaiting Documents',
                'is_posted' => true,
            ]);

            return redirect()->route('account.rentals')->with('success', 'Payment successful! Your rental booking is now active. Please upload your rental agreement.');

        } catch (\Exception $e) {
            Log::error('Error processing payment success', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);

            return redirect()->route('account.rentals')->with('error', 'Payment processed but an error occurred. Please contact support.');
        }
    }

    /**
     * Handle Judopay failure callback
     */
    public function failure(Request $request)
    {
        Log::warning('Judopay payment failure callback', $request->all());

        return redirect()->route('account.rentals')->with('error', 'Payment was cancelled or failed. Please try again.');
    }
}
