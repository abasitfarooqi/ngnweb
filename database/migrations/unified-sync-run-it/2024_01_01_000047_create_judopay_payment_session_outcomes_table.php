<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('judopay_payment_session_outcomes'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_payment_session_outcomes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_type` varchar(255) NOT NULL,
  `session_id` bigint(20) unsigned NOT NULL,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `status` enum('success','declined','refunded','expired','cancelled','error') NOT NULL,
  `payment_network_transaction_id` varchar(255) DEFAULT NULL COMMENT 'Payment Network Transaction ID',
  `locator_id` varchar(255) DEFAULT NULL COMMENT 'JudoPay internal locator ID for support queries',
  `disable_network_tokenisation` tinyint(1) DEFAULT NULL COMMENT 'Network tokenisation setting used',
  `allow_increment` tinyint(1) DEFAULT NULL COMMENT 'Whether incremental authorisation was allowed',
  `acquirer_transaction_id` varchar(255) DEFAULT NULL COMMENT 'Acquirer Transaction ID',
  `auth_code` varchar(255) DEFAULT NULL COMMENT 'Auth Code',
  `external_bank_response_code` varchar(255) DEFAULT NULL COMMENT 'External Bank Response Code',
  `bank_response_category` varchar(255) DEFAULT NULL COMMENT 'Categorised bank response: SUCCESS, DECLINED, INSUFFICIENT_FUNDS, etc.',
  `is_retryable` tinyint(1) DEFAULT NULL COMMENT 'Whether this decline type should be retried',
  `appears_on_statement_as` varchar(255) DEFAULT NULL COMMENT 'Appears On Statement As',
  `merchant_name` varchar(255) DEFAULT NULL COMMENT 'Merchant name from JudoPay response',
  `judo_id` varchar(255) DEFAULT NULL COMMENT 'JudoPay merchant ID used for this transaction',
  `card_last_four` varchar(4) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardLastfour - Last 4 digits of card (non-PCI)',
  `card_funding` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardFunding - Card type: Credit/Debit',
  `card_category` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCategory - Card category: Classic/Premium/etc',
  `card_country` varchar(2) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCountry - ISO country code of card issuer',
  `issuing_bank` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.bank - Name of card issuing bank',
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'From webhook receipt.billingAddress - Customer billing address used for payment' CHECK (json_valid(`billing_address`)),
  `risk_assessment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'From webhook receipt.risks - Risk checks: postCodeCheck, cv2Check, merchantSuggestion' CHECK (json_valid(`risk_assessment`)),
  `three_d_secure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'From webhook receipt.threeDSecure - 3DS authentication details and challenge results' CHECK (json_valid(`three_d_secure`)),
  `risk_score` tinyint(3) unsigned DEFAULT NULL COMMENT 'JudoPay risk score (0-100)',
  `recurring_payment_type` varchar(255) DEFAULT NULL COMMENT 'CIT or MIT for recurring payment classification',
  `type` varchar(255) DEFAULT NULL COMMENT 'Type',
  `source` enum('api','webhook','manual','system','failure','success') NOT NULL DEFAULT 'api' COMMENT 'Origin of the outcome event',
  `judopay_receipt_id` varchar(255) DEFAULT NULL COMMENT 'Receipt ID tied to this outcome',
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'Amount relevant to this outcome (e.g., refund amount)',
  `net_amount` decimal(10,2) DEFAULT NULL COMMENT 'Net amount after fees (different from original amount)',
  `original_amount` decimal(10,2) DEFAULT NULL COMMENT 'Original requested amount before any adjustments',
  `amount_collected` decimal(10,2) DEFAULT NULL COMMENT 'Actual amount collected (0.00 for declined payments)',
  `your_payment_reference` varchar(255) DEFAULT NULL COMMENT 'yourPaymentReference from JudoPay payload',
  `your_consumer_reference` varchar(255) DEFAULT NULL COMMENT 'yourConsumerReference from JudoPay payload',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Raw webhook/API payload for this event' CHECK (json_valid(`payload`)),
  `message` text DEFAULT NULL COMMENT 'Free-form reason or message',
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `judopay_created_at` timestamp NULL DEFAULT NULL COMMENT 'Original JudoPay transaction timestamp',
  `timezone` varchar(255) DEFAULT NULL COMMENT 'Timezone of the original transaction',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `judopay_payment_session_outcomes_session_type_session_id_index` (`session_type`,`session_id`),
  KEY `judopay_payment_session_outcomes_subscription_id_foreign` (`subscription_id`),
  KEY `idx_bank_response` (`external_bank_response_code`,`bank_response_category`),
  KEY `idx_risk_status` (`risk_score`,`status`),
  KEY `idx_created_status` (`judopay_created_at`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=986 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_payment_session_outcomes');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
