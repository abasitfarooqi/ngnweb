<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('judopay_subscriptions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `judopay_onboarding_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL COMMENT 'date of subscription Despite the outcome(status)',
  `subscribable_type` varchar(255) NOT NULL,
  `subscribable_id` bigint(20) unsigned NOT NULL,
  `billing_frequency` enum('weekly','monthly','custom') NOT NULL COMMENT 'Payment frequency',
  `billing_day` int(11) DEFAULT NULL COMMENT 'Day of week (1-7) for weekly, day of month (1-28) for monthly',
  `amount` decimal(10,2) NOT NULL COMMENT 'Recurring payment amount. Require for both rental and finance.',
  `opening_balance` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Existing balance at the time of onboarding. 0 if rental.',
  `start_date` date NOT NULL COMMENT 'The date of the first payment. If rental. It is the date of the rental date.',
  `end_date` date DEFAULT NULL COMMENT 'At the time of onboarding, the expected end date of the contract. In case of Rental null. Rental require manual stop subscription.',
  `status` enum('pending','active','inactive','paused','completed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'They all are more like flags. Only active is where the recurring payment got fired. It is NGN flag',
  `consumer_reference` varchar(255) NOT NULL COMMENT 'JudoPay consumer reference format: RENTAL/FINANCE/CUSTOMER ID \n                        e.g. HIRE-BOOKINGID-CUSTOMERID or FIN-CONTRACTID-CUSTOMERID. FIN-12-201 / HIR-12-201',
  `card_token` text DEFAULT NULL COMMENT 'The Card Token to be used for MIT - ENCRYPTED PCI SENSITIVE.',
  `receipt_id` varchar(255) DEFAULT NULL COMMENT 'CIT successful trnasaction gives the receipt id. preserve to use for MIT.',
  `judopay_receipt_id` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.receiptId - JudoPay unique transaction identifier',
  `acquirer_transaction_id` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.acquirerTransactionId - Bank transaction reference',
  `auth_code` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.authCode - Bank authorization code for successful payment',
  `merchant_name` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.merchantName - Merchant name as registered with JudoPay',
  `statement_descriptor` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.appearsOnStatementAs - How transaction appears on customer statement',
  `card_last_four` varchar(4) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardLastfour - Last 4 digits of card (non-PCI)',
  `card_funding` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardFunding - Card type: Credit/Debit',
  `card_category` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCategory - Card category: Classic/Premium/etc',
  `card_country` varchar(2) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCountry - ISO country code of card issuer',
  `issuing_bank` varchar(255) DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.bank - Name of card issuing bank',
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'From webhook receipt.billingAddress - Customer billing address used for payment' CHECK (json_valid(`billing_address`)),
  `risk_assessment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'From webhook receipt.risks - Risk checks: postCodeCheck, cv2Check, merchantSuggestion' CHECK (json_valid(`risk_assessment`)),
  `three_d_secure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'From webhook receipt.threeDSecure - 3DS authentication details and challenge results' CHECK (json_valid(`three_d_secure`)),
  `audit_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Audit trail. Important states changes dates.' CHECK (json_valid(`audit_log`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `judopay_subscriptions_judopay_onboarding_id_foreign` (`judopay_onboarding_id`),
  KEY `judopay_subscriptions_subscribable_type_subscribable_id_index` (`subscribable_type`,`subscribable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_subscriptions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
