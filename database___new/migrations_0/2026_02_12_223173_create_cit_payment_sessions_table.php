<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('judopay_cit_payment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('judopay_subscriptions')->restrictOnDelete();

            // JUDOPAY related fields
            $table->string('judopay_payment_reference')->unique()
                ->comment('Unique payment reference for this session. Always prefix with consumer reference eg. cit-consumerreference-timestamp');

            // Payment details (captured at session creation)
            $table->decimal('amount', 10, 2)->comment('Amount for this session');

            // Customer details (captured at session creation for consistency) - ENCRYPTED GDPR PII
            $table->text('customer_email')->comment('Customer email at time of session creation - ENCRYPTED');
            $table->text('customer_mobile')->nullable()->comment('Customer mobile at time of session creation - ENCRYPTED');
            $table->text('customer_name')->comment('Customer name for card holder - ENCRYPTED');

            // Card Holder details / Billing address - ENCRYPTED GDPR PII
            $table->text('card_holder_name')->comment('Card holder name for card payment - ENCRYPTED');
            $table->text('address1')->comment('Address line 1 for card payment - ENCRYPTED');
            $table->text('address2')->comment('Address line 2 for card payment - ENCRYPTED');
            $table->text('city')->comment('Town/city for card payment - ENCRYPTED');
            $table->text('postcode')->nullable()->comment('Postcode for card payment - ENCRYPTED');

            // JudoPay reference and paylink url
            $table->string('judopay_reference')->nullable()->comment('JudoPay reference returned from /webpayments/payments');
            $table->string('judopay_receipt_id')->nullable()->comment('The Receipt ID to be used for subscription.');
            $table->text('judopay_paylink_url')->nullable()->comment('payByLinkUrl from JudoPay response (can be long)');
            $table->text('card_token')->nullable()->comment('The obtained Card Token in the result of Successful CIT');

            // Session configuration
            $table->timestamp('expiry_date')->comment('Session expiry time (24 hours from creation)');

            // Session status (added 'error' for consistency with other tables)
            $table->enum('status', [
                'created',  // Link Generated, not yet sent or accessed by customer.
                'success',  // Webhook dispatched, payment successful message.
                'declined', // Webhook dispatched, payment declined message.
                'refunded', // Webhook dispatched, payment refunded message or reverse initiated.
                'expired',  // Session expired.
                'cancelled', // Session manually cancelled
                'error',     // Pre-bank validation error
            ])->default('created');

            $table->boolean('is_active')->default(true)->comment('Each Consumer Reference should have only one active session. whether outcome is success, declined, refunded or expired.');

            // Consent tracking fields
            $table->timestamp('consent_given_at')->nullable()->comment('When customer ticked consent checkbox');
            $table->string('consent_ip_address')->nullable()->comment('Customer IP address at consent time');
            $table->string('consent_terms_version')->nullable()->comment('Version of terms accepted (e.g., v1.0-judopay-cit)');
            $table->string('consent_content_sha256', 64)->nullable()
                ->comment('SHA-256 hash of consent text shown to customer for audit trail');
            
            // SMS verification tracking
            $table->string('sms_verification_sid')->nullable()->comment('Twilio SMS SID linking to sms_messages.sid');
            $table->timestamp('sms_verified_at')->nullable()->comment('When SMS code was successfully verified');

            // JudoPay response tracking
            $table->json('judopay_response')->nullable()->comment('NOT SENSITIVE.Recent Full response from JudoPay session creation');
            $table->json('judopay_webhook_data')->nullable()->comment('Recent Webhook data from JudoPay');
            $table->json('judopay_session_status')->nullable()->comment('Recent Session status from JudoPay, if additional call made from get request to check recept / reference ');

            $table->integer('status_score')->default(0)->comment('CIT need 2, MIT need 1.');

            $table->timestamp('payment_completed_at')->nullable();

            // Audit trail
            $table->timestamp('link_generated_at')->nullable();
            $table->timestamp('customer_accessed_at')->nullable()->comment('UI Redirect to Judopay from Hostapplication');
            $table->text('failure_reason')->nullable();

            $table->timestamps();
        });
    }
};
