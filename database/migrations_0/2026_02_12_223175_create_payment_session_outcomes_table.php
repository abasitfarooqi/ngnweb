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
        Schema::create('judopay_payment_session_outcomes', function (Blueprint $table) {
            $table->id();

            // Polymorphic link to either CIT or MIT session (using cleaner naming)
            $table->morphs('session'); // session_id, session_type

            // Denormalized for reporting/filtering
            $table->foreignId('subscription_id')->constrained('judopay_subscriptions')->restrictOnDelete();

            // Outcome classification (added 'error' for pre-bank validation failures)
            $table->enum('status', [
                'success',
                'declined',
                'refunded',
                'expired',
                'cancelled',
                'error',
            ]);

            $table->string('payment_network_transaction_id')->nullable()->comment('Payment Network Transaction ID');

            // Enhanced transaction tracking (MERGED)
            $table->string('locator_id')->nullable()->comment('JudoPay internal locator ID for support queries');
            $table->boolean('disable_network_tokenisation')->nullable()->comment('Network tokenisation setting used');
            $table->boolean('allow_increment')->nullable()->comment('Whether incremental authorisation was allowed');

            $table->string('acquirer_transaction_id')->nullable()->comment('Acquirer Transaction ID');
            $table->string('auth_code')->nullable()->comment('Auth Code');
            $table->string('external_bank_response_code')->nullable()->comment('External Bank Response Code');

            // Bank response classification (MERGED)
            $table->string('bank_response_category')->nullable()->comment('Categorised bank response: SUCCESS, DECLINED, INSUFFICIENT_FUNDS, etc.');
            $table->boolean('is_retryable')->nullable()->comment('Whether this decline type should be retried');

            $table->string('appears_on_statement_as')->nullable()->comment('Appears On Statement As');

            // Critical compliance fields (MERGED)
            $table->string('merchant_name')->nullable()->comment('Merchant name from JudoPay response');
            $table->string('judo_id')->nullable()->comment('JudoPay merchant ID used for this transaction');

            // Non-sensitive card details (safe to store)
            $table->string('card_last_four', 4)->nullable()->comment('From webhook receipt.cardDetails.cardLastfour - Last 4 digits of card (non-PCI)');
            $table->string('card_funding')->nullable()->comment('From webhook receipt.cardDetails.cardFunding - Card type: Credit/Debit');
            $table->string('card_category')->nullable()->comment('From webhook receipt.cardDetails.cardCategory - Card category: Classic/Premium/etc');
            $table->string('card_country', 2)->nullable()->comment('From webhook receipt.cardDetails.cardCountry - ISO country code of card issuer');
            $table->string('issuing_bank')->nullable()->comment('From webhook receipt.cardDetails.bank - Name of card issuing bank');

            // Billing address (non-PCI data for dispute resolution and compliance)
            $table->json('billing_address')->nullable()->comment('From webhook receipt.billingAddress - Customer billing address used for payment');

            // Risk and fraud assessment (important for future transactions)
            $table->json('risk_assessment')->nullable()->comment('From webhook receipt.risks - Risk checks: postCodeCheck, cv2Check, merchantSuggestion');

            // 3D Secure authentication details (compliance and security)
            $table->json('three_d_secure')->nullable()->comment('From webhook receipt.threeDSecure - 3DS authentication details and challenge results');

            // Risk and compliance enhancement (MERGED)
            $table->unsignedTinyInteger('risk_score')->nullable()->comment('JudoPay risk score (0-100)');
            $table->string('recurring_payment_type')->nullable()->comment('CIT or MIT for recurring payment classification');

            $table->string('type')->nullable()->comment('Type');

            $table->enum('source', ['api', 'webhook', 'manual', 'system', 'failure', 'success'])->default('api')
                ->comment('Origin of the outcome event');

            // Judopay identifiers and values (increased field lengths to 64 chars)
            $table->string('judopay_receipt_id')->nullable()->comment('Receipt ID tied to this outcome');
            $table->decimal('amount', 10, 2)->nullable()->comment('Amount relevant to this outcome (e.g., refund amount)');

            // Enhanced amount tracking (MERGED)
            $table->decimal('net_amount', 10, 2)->nullable()->comment('Net amount after fees (different from original amount)');
            $table->decimal('original_amount', 10, 2)->nullable()->comment('Original requested amount before any adjustments');
            $table->decimal('amount_collected', 10, 2)->nullable()->comment('Actual amount collected (0.00 for declined payments)');

            // Reference fields for reconciliation without joins
            $table->string('your_payment_reference')->nullable()->comment('yourPaymentReference from JudoPay payload');
            $table->string('your_consumer_reference')->nullable()->comment('yourConsumerReference from JudoPay payload');

            // Raw data and notes
            $table->json('payload')->nullable()->comment('Raw webhook/API payload for this event');
            $table->text('message')->nullable()->comment('Free-form reason or message');

            $table->timestamp('occurred_at')->useCurrent();

            // Compliance and audit (MERGED)
            $table->timestamp('judopay_created_at')->nullable()->comment('Original JudoPay transaction timestamp');
            $table->string('timezone')->nullable()->comment('Timezone of the original transaction');

            $table->timestamps();

            // Add indexes for common queries (MERGED)
            $table->index(['external_bank_response_code', 'bank_response_category'], 'idx_bank_response');
            $table->index(['risk_score', 'status'], 'idx_risk_status');
            $table->index(['judopay_created_at', 'status'], 'idx_created_status');

            // Deduplication constraint for webhooks (prevents duplicate processing)
            // $table->unique(['session_type', 'session_id', 'judopay_receipt_id'], 'uniq_session_receipt');
        });
    }
};
