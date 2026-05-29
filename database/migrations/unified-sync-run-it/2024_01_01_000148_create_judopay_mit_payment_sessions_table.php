<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('judopay_mit_payment_sessions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_mit_payment_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `judopay_payment_reference` varchar(255) NOT NULL COMMENT 'yourPaymentReference for this MIT run. Prefix with consumer reference, e.g. mit-<consumerRef>-<timestamp>',
  `amount` decimal(10,2) NOT NULL COMMENT 'Amount for this MIT run',
  `order_reference` varchar(255) DEFAULT NULL COMMENT 'yourPaymentMetaData.order_reference (e.g., invoice number)',
  `description` varchar(255) DEFAULT NULL COMMENT 'yourPaymentMetaData.description (human-readable label)',
  `judopay_related_receipt_id` varchar(255) DEFAULT NULL COMMENT 'relatedReceiptId used for this MIT (CIT receipt ID)',
  `card_token_used` text DEFAULT NULL COMMENT 'Card token actually used for this MIT attempt (audit trail)',
  `judopay_receipt_id` varchar(255) DEFAULT NULL COMMENT 'Receipt ID returned by JudoPay for this MIT transaction',
  `judopay_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Full response from JudoPay transactions/payments' CHECK (json_valid(`judopay_response`)),
  `status` enum('created','success','declined','refunded','cancelled','error') NOT NULL DEFAULT 'created',
  `status_score` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '0=created, 1=api_success, 2=webhook_confirmed_success (like CIT scoring system)',
  `scheduled_for` timestamp NULL DEFAULT NULL COMMENT 'Planned execution time for scheduler',
  `payment_completed_at` timestamp NULL DEFAULT NULL,
  `attempt_no` smallint(5) unsigned NOT NULL DEFAULT 1 COMMENT 'Attempt number within the billing cycle',
  `failure_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_mit_payment_sessions_judopay_payment_reference_unique` (`judopay_payment_reference`),
  KEY `judopay_mit_payment_sessions_subscription_id_foreign` (`subscription_id`),
  KEY `judopay_mit_payment_sessions_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=535 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_mit_payment_sessions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
