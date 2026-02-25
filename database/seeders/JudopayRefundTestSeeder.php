<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayOnboarding;
use App\Models\JudopayPaymentSessionOutcome;
use App\Models\JudopaySubscription;
use App\Models\RentingBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JudopayRefundTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates test data for refund testing
     */
    public function run(): void
    {
        $this->command->info('Creating JudoPay refund test data...');

        // Use existing user ID 93 (as per user preference)
        $userId = 93;
        $user = User::find($userId);
        if (!$user) {
            $this->command->error("User ID {$userId} not found. Please ensure user exists.");
            return;
        }

        $this->command->info("Using user ID: {$userId}");

        // Find or create a test customer
        $customer = Customer::firstOrCreate(
            ['email' => 'refund-test@example.com'],
            [
                'first_name' => 'Refund',
                'last_name' => 'Test',
                'phone' => '07123456789',
                'address' => '123 Test Street',
                'city' => 'London',
                'postcode' => 'SW1A 1AA',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("Using customer ID: {$customer->id}");

        // Find or create a test renting booking
        $booking = RentingBooking::firstOrCreate(
            ['customer_id' => $customer->id, 'is_posted' => true],
            [
                'user_id' => $userId,
                'start_date' => now()->subDays(30),
                'due_date' => null,
                'state' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("Using booking ID: {$booking->id}");

        // Create onboarding record
        $onboarding = JudopayOnboarding::firstOrCreate(
            [
                'onboardable_id' => $customer->id,
                'onboardable_type' => Customer::class,
            ],
            [
                'is_onboarded' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("Using onboarding ID: {$onboarding->id}");

        // Create subscription
        $subscription = JudopaySubscription::firstOrCreate(
            [
                'subscribable_id' => $booking->id,
                'subscribable_type' => RentingBooking::class,
            ],
            [
                'judopay_onboarding_id' => $onboarding->id,
                'date' => now()->toDateString(),
                'billing_frequency' => 'weekly',
                'billing_day' => 1,
                'amount' => 50.00,
                'opening_balance' => 0,
                'start_date' => now()->subDays(30),
                'end_date' => null,
                'status' => 'active',
                'consumer_reference' => 'NGNR-TEST-' . $booking->id . '-' . $customer->id,
                'card_token' => 'TEST_CARD_TOKEN_' . str()->random(20),
                'judopay_receipt_id' => 'TEST_RECEIPT_' . str()->random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("Using subscription ID: {$subscription->id}");

        // Create successful CIT payment session for refund testing
        $receiptId = 'TEST_RECEIPT_' . str()->random(10);
        $paymentReference = 'CIT-TEST-' . now()->timestamp;
        
        $citSession = JudopayCitPaymentSession::create([
            'subscription_id' => $subscription->id,
            'user_id' => $userId,
            'judopay_payment_reference' => $paymentReference,
            'amount' => 50.00,
            'customer_email' => $customer->email,
            'customer_mobile' => $customer->phone,
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'card_holder_name' => $customer->first_name . ' ' . $customer->last_name,
            'address1' => $customer->address,
            'address2' => $customer->address,
            'city' => $customer->city,
            'postcode' => $customer->postcode,
            'judopay_reference' => 'TEST_REF_' . str()->random(10),
            'judopay_receipt_id' => $receiptId,
            'judopay_paylink_url' => 'https://payments-sandbox.judopay.com/paylink/TEST',
            'expiry_date' => now()->addHours(24),
            'status' => 'success',
            'is_active' => false,
            'card_token' => 'TEST_CARD_TOKEN_' . str()->random(20),
            'judopay_response' => [
                'receiptId' => $receiptId,
                'result' => 'Success',
                'amount' => 50.00,
            ],
            'judopay_webhook_data' => [
                'receiptId' => $receiptId,
                'result' => 'Success',
                'amount' => 50.00,
            ],
            'payment_completed_at' => now()->subHours(1),
            'link_generated_at' => now()->subHours(2),
            'status_score' => 2,
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(1),
        ]);

        $this->command->info("Created CIT session ID: {$citSession->id}");

        // Create success outcome record with realistic payment details
        $successOutcome = JudopayPaymentSessionOutcome::create([
            'session_id' => $citSession->id,
            'session_type' => 'App\Models\JudopayCitPaymentSession',
            'subscription_id' => $subscription->id,
            'status' => 'success',
            'source' => 'webhook',
            'judopay_receipt_id' => $receiptId,
            'payment_network_transaction_id' => 'TXN_' . str()->random(15),
            'acquirer_transaction_id' => 'ACQ_' . str()->random(12),
            'auth_code' => str()->random(6),
            'external_bank_response_code' => '00',
            'appears_on_statement_as' => 'NGN MOTORS',
            'card_last_four' => '4242',
            'card_funding' => 'Credit',
            'card_category' => 'Consumer',
            'card_country' => 'GB',
            'issuing_bank' => 'Test Bank',
            'amount' => 50.00,
            'your_payment_reference' => $paymentReference,
            'your_consumer_reference' => $subscription->consumer_reference,
            'merchant_name' => 'NGN Motors',
            'judo_id' => 'JUDO_' . str()->random(10),
            'net_amount' => 48.50,
            'original_amount' => 50.00,
            'amount_collected' => 50.00,
            'risk_score' => 25,
            'bank_response_category' => 'Success',
            'recurring_payment_type' => 'cit',
            'payload' => [
                'receiptId' => $receiptId,
                'result' => 'Success',
                'amount' => 50.00,
                'cardDetails' => [
                    'cardLastfour' => '4242',
                    'cardFunding' => 'Credit',
                    'cardCategory' => 'Consumer',
                    'cardCountry' => 'GB',
                    'bank' => 'Test Bank',
                ],
                'acquirerTransactionId' => 'ACQ_' . str()->random(12),
                'paymentNetworkTransactionId' => 'TXN_' . str()->random(15),
                'riskScore' => 25,
                'externalBankResponseCode' => '00',
                'netAmount' => 48.50,
                'amountCollected' => 50.00,
                'merchantName' => 'NGN Motors',
                'judoId' => 'JUDO_' . str()->random(10),
                'yourPaymentReference' => $paymentReference,
                'consumer' => [
                    'yourConsumerReference' => $subscription->consumer_reference,
                ],
            ],
            'message' => 'Payment successful',
            'occurred_at' => now()->subHours(1),
            'created_at' => now()->subHours(1),
            'updated_at' => now()->subHours(1),
        ]);

        $this->command->info("Created success outcome ID: {$successOutcome->id}");

        $this->command->info('');
        $this->command->info('=== Refund Test Data Created ===');
        $this->command->info("Customer ID: {$customer->id}");
        $this->command->info("Customer Email: {$customer->email}");
        $this->command->info("Subscription ID: {$subscription->id}");
        $this->command->info("CIT Session ID: {$citSession->id}");
        $this->command->info("Receipt ID: {$receiptId}");
        $this->command->info("Payment Reference: {$paymentReference}");
        $this->command->info('');
        $this->command->info('To test refund:');
        $this->command->info("1. Go to /admin/judopay/subscribe/{$subscription->id}");
        $this->command->info('2. Find the successful CIT session in the table');
        $this->command->info('3. Click the red "Refund" button in the Actions column');
        $this->command->info('4. Confirm the refund action');
        $this->command->info('');
        $this->command->info('Note: Make sure JUDOPAY_CIT_REFUND_MODE=manual in .env');
        $this->command->info('');
    }
}
