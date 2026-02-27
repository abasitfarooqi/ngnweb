<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judopay_enquiry_records', function (Blueprint $table) {
            $table->id();

            // Link to the outcome (source of truth) - both CIT and MIT have outcomes
            // NULL for standalone enquiries (accountant/manual verification)
            $table->foreignId('payment_session_outcome_id')
                ->nullable()
                ->constrained('judopay_payment_session_outcomes')
                ->restrictOnDelete()
                ->comment('Links to the outcome record - NULL for standalone enquiries');

            // Enquiry details
            $table->enum('enquiry_type', ['webpayment', 'transaction'])
                ->comment('webpayment for CIT (uses reference), transaction for MIT (uses receiptId)');

            $table->string('enquiry_identifier')
                ->comment('Either reference (CIT) or receiptId (MIT) used for the enquiry');

            $table->string('endpoint_used')
                ->comment('The actual endpoint called (e.g., /webpayments/ABC123 or /transactions/receipt456)');

            // API Response
            $table->enum('api_status', ['success', 'failed', 'timeout', 'error'])
                ->comment('HTTP response status category');

            $table->integer('http_status_code')
                ->nullable()
                ->comment('Actual HTTP status code (200, 404, 500, etc.)');

            $table->json('api_response')
                ->nullable()
                ->comment('Full JSON response from JudoPay API');

            $table->json('api_headers')
                ->nullable()
                ->comment('Response headers for debugging');

            // Analysis fields (populated after response analysis)
            $table->string('judopay_status')
                ->nullable()
                ->comment('Status from JudoPay response: Success, Declined, etc.');

            $table->string('current_state')
                ->nullable()
                ->comment('Current transaction state according to JudoPay');

            $table->boolean('matches_local_record')
                ->nullable()
                ->comment('Does the enquiry response match our local outcome record?');

            $table->text('discrepancy_notes')
                ->nullable()
                ->comment('Detailed analysis notes about any discrepancies found');

            // Additional fields based on real API responses
            $table->string('external_bank_response_code')
                ->nullable()
                ->comment('Bank response code from JudoPay (0=success, 5=declined, etc.)');

            $table->decimal('amount_collected_remote', 10, 2)
                ->nullable()
                ->comment('Amount collected according to JudoPay (0.00 for declined)');

            $table->string('remote_message')
                ->nullable()
                ->comment('Message from JudoPay (AuthCode: 123456 or Card declined)');

            $table->boolean('is_retryable')
                ->nullable()
                ->comment('Based on enquiry analysis, should this be retried?');

            // Metadata
            $table->timestamp('enquired_at')
                ->comment('When the enquiry was made');

            $table->string('enquiry_reason')
                ->comment('Why this enquiry was made (retry_check, manual_verification, reconciliation, etc.)');

            $table->timestamps();

            // Indexes for common queries
            $table->index(['payment_session_outcome_id', 'enquiry_type'], 'idx_outcome_enquiry');
            $table->index(['enquiry_identifier', 'enquiry_type'], 'idx_identifier_type');
            $table->index(['enquired_at', 'api_status'], 'idx_enquired_status');
            $table->index(['judopay_status', 'matches_local_record'], 'idx_status_match');
            $table->index(['external_bank_response_code', 'is_retryable'], 'idx_bank_retry');
            $table->index(['enquiry_reason', 'enquired_at'], 'idx_reason_date');
        });
    }
};
