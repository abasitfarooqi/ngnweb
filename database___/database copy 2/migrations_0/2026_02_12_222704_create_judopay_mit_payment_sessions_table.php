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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscription_id')->index('judopay_mit_payment_sessions_subscription_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('judopay_mit_payment_sessions_user_id_foreign');
            $table->string('judopay_payment_reference')->unique()->comment('yourPaymentReference for this MIT run. Prefix with consumer reference, e.g. mit-<consumerRef>-<timestamp>');
            $table->decimal('amount', 10)->comment('Amount for this MIT run');
            $table->string('order_reference')->nullable()->comment('yourPaymentMetaData.order_reference (e.g., invoice number)');
            $table->string('description')->nullable()->comment('yourPaymentMetaData.description (human-readable label)');
            $table->string('judopay_related_receipt_id')->nullable()->comment('relatedReceiptId used for this MIT (CIT receipt ID)');
            $table->text('card_token_used')->nullable()->comment('Card token actually used for this MIT attempt (audit trail)');
            $table->string('judopay_receipt_id')->nullable()->comment('Receipt ID returned by JudoPay for this MIT transaction');
            $table->json('judopay_response')->nullable()->comment('Full response from JudoPay transactions/payments');
            $table->enum('status', ['created', 'success', 'declined', 'refunded', 'cancelled', 'error'])->default('created');
            $table->unsignedSmallInteger('status_score')->default(0)->comment('0=created, 1=api_success, 2=webhook_confirmed_success (like CIT scoring system)');
            $table->timestamp('scheduled_for')->nullable()->comment('Planned execution time for scheduler');
            $table->timestamp('payment_completed_at')->nullable();
            $table->unsignedSmallInteger('attempt_no')->default(1)->comment('Attempt number within the billing cycle');
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judopay_mit_payment_sessions');
    }
};
