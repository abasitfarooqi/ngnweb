<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_enquiry_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_session_outcome_id` bigint unsigned DEFAULT NULL,
  `enquiry_type` enum('webpayment','transaction') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'webpayment for CIT (uses reference), transaction for MIT (uses receiptId)',
  `enquiry_identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Either reference (CIT) or receiptId (MIT) used for the enquiry',
  `endpoint_used` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The actual endpoint called (e.g., /webpayments/ABC123 or /transactions/receipt456)',
  `api_status` enum('success','failed','timeout','error') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP response status category',
  `http_status_code` int DEFAULT NULL COMMENT 'Actual HTTP status code (200, 404, 500, etc.)',
  `api_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Full JSON response from JudoPay API',
  `api_headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Response headers for debugging',
  `judopay_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status from JudoPay response: Success, Declined, etc.',
  `current_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Current transaction state according to JudoPay',
  `matches_local_record` tinyint(1) DEFAULT NULL COMMENT 'Does the enquiry response match our local outcome record?',
  `discrepancy_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Detailed analysis notes about any discrepancies found',
  `external_bank_response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank response code from JudoPay (0=success, 5=declined, etc.)',
  `amount_collected_remote` decimal(10,2) DEFAULT NULL COMMENT 'Amount collected according to JudoPay (0.00 for declined)',
  `remote_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Message from JudoPay (AuthCode: 123456 or Card declined)',
  `is_retryable` tinyint(1) DEFAULT NULL COMMENT 'Based on enquiry analysis, should this be retried?',
  `enquired_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'When the enquiry was made',
  `enquiry_reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Why this enquiry was made (retry_check, manual_verification, reconciliation, etc.)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_outcome_enquiry` (`payment_session_outcome_id`,`enquiry_type`),
  KEY `idx_identifier_type` (`enquiry_identifier`,`enquiry_type`),
  KEY `idx_enquired_status` (`enquired_at`,`api_status`),
  KEY `idx_status_match` (`judopay_status`,`matches_local_record`),
  KEY `idx_bank_retry` (`external_bank_response_code`,`is_retryable`),
  KEY `idx_reason_date` (`enquiry_reason`,`enquired_at`),
  CONSTRAINT `judopay_enquiry_records_chk_1` CHECK (json_valid(`api_response`)),
  CONSTRAINT `judopay_enquiry_records_chk_2` CHECK (json_valid(`api_headers`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_enquiry_records');
    }
};
