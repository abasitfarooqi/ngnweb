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
        Schema::create('judopay_enquiry_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_session_outcome_id')->nullable();
            $table->enum('enquiry_type', ['webpayment', 'transaction'])->comment('webpayment for CIT (uses reference), transaction for MIT (uses receiptId)');
            $table->string('enquiry_identifier')->comment('Either reference (CIT) or receiptId (MIT) used for the enquiry');
            $table->string('endpoint_used')->comment('The actual endpoint called (e.g., /webpayments/ABC123 or /transactions/receipt456)');
            $table->enum('api_status', ['success', 'failed', 'timeout', 'error'])->comment('HTTP response status category');
            $table->integer('http_status_code')->nullable()->comment('Actual HTTP status code (200, 404, 500, etc.)');
            $table->json('api_response')->nullable()->comment('Full JSON response from JudoPay API');
            $table->json('api_headers')->nullable()->comment('Response headers for debugging');
            $table->string('judopay_status')->nullable()->comment('Status from JudoPay response: Success, Declined, etc.');
            $table->string('current_state')->nullable()->comment('Current transaction state according to JudoPay');
            $table->boolean('matches_local_record')->nullable()->comment('Does the enquiry response match our local outcome record?');
            $table->text('discrepancy_notes')->nullable()->comment('Detailed analysis notes about any discrepancies found');
            $table->string('external_bank_response_code')->nullable()->comment('Bank response code from JudoPay (0=success, 5=declined, etc.)');
            $table->decimal('amount_collected_remote', 10)->nullable()->comment('Amount collected according to JudoPay (0.00 for declined)');
            $table->string('remote_message')->nullable()->comment('Message from JudoPay (AuthCode: 123456 or Card declined)');
            $table->boolean('is_retryable')->nullable()->comment('Based on enquiry analysis, should this be retried?');
            $table->timestamp('enquired_at')->useCurrentOnUpdate()->useCurrent()->comment('When the enquiry was made');
            $table->string('enquiry_reason')->comment('Why this enquiry was made (retry_check, manual_verification, reconciliation, etc.)');
            $table->timestamps();

            $table->index(['external_bank_response_code', 'is_retryable'], 'idx_bank_retry');
            $table->index(['enquired_at', 'api_status'], 'idx_enquired_status');
            $table->index(['enquiry_identifier', 'enquiry_type'], 'idx_identifier_type');
            $table->index(['payment_session_outcome_id', 'enquiry_type'], 'idx_outcome_enquiry');
            $table->index(['enquiry_reason', 'enquired_at'], 'idx_reason_date');
            $table->index(['judopay_status', 'matches_local_record'], 'idx_status_match');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judopay_enquiry_records');
    }
};
