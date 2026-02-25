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
        Schema::create('judopay_mit_payment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('judopay_subscriptions')->restrictOnDelete();

            // JudoPay references
            $table->string('judopay_payment_reference')->unique()
                ->comment('yourPaymentReference for this MIT run. Prefix with consumer reference, e.g. mit-<consumerRef>-<timestamp>');

            // Payment details (per run)
            $table->decimal('amount', 10, 2)->comment('Amount for this MIT run');
            $table->string('order_reference')->nullable()->comment('yourPaymentMetaData.order_reference (e.g., invoice number)');
            $table->string('description')->nullable()->comment('yourPaymentMetaData.description (human-readable label)');

            // Linkage to original CIT receipt used for MIT
            $table->string('judopay_related_receipt_id')->nullable()->comment('relatedReceiptId used for this MIT (CIT receipt ID)');

            // Card token audit trail
            $table->text('card_token_used')->nullable()->comment('Card token actually used for this MIT attempt (audit trail)');;

            // Response artifacts
            $table->string('judopay_receipt_id')->nullable()->comment('Receipt ID returned by JudoPay for this MIT transaction');
            $table->json('judopay_response')->nullable()->comment('Full response from JudoPay transactions/payments');

            // Lifecycle (added 'error' for pre-bank validation failures)
            $table->enum('status', [
                'created',   // Record created, not yet attempted
                'success',   // Payment succeeded
                'declined',  // Payment declined
                'refunded',  // Payment refunded (full/partial)
                'cancelled', // Attempt cancelled/skipped
                'error',      // Pre-bank validation error (e.g., invalid relatedReceiptId)
            ])->default('created');

            $table->unsignedSmallInteger('status_score')->default(0)
                ->comment('0=created, 1=api_success, 2=webhook_confirmed_success (like CIT scoring system)');

            $table->timestamp('scheduled_for')->nullable()->comment('Planned execution time for scheduler');
            $table->timestamp('payment_completed_at')->nullable();
            $table->unsignedSmallInteger('attempt_no')->default(1)->comment('Attempt number within the billing cycle');
            $table->text('failure_reason')->nullable();

            $table->timestamps();
        });
    }
};
