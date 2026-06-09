<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_cit_payment_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `judopay_payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique payment reference for this session. Always prefix with consumer reference eg. cit-consumerreference-timestamp',
  `amount` decimal(10,2) NOT NULL COMMENT 'Amount for this session',
  `customer_email` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer email at time of session creation - ENCRYPTED',
  `customer_mobile` text COLLATE utf8mb4_unicode_ci COMMENT 'Customer mobile at time of session creation - ENCRYPTED',
  `customer_name` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer name for card holder - ENCRYPTED',
  `card_holder_name` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Card holder name for card payment - ENCRYPTED',
  `address1` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address line 1 for card payment - ENCRYPTED',
  `address2` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address line 2 for card payment - ENCRYPTED',
  `city` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Town/city for card payment - ENCRYPTED',
  `postcode` text COLLATE utf8mb4_unicode_ci COMMENT 'Postcode for card payment - ENCRYPTED',
  `judopay_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JudoPay reference returned from /webpayments/payments',
  `judopay_receipt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The Receipt ID to be used for subscription.',
  `judopay_paylink_url` text COLLATE utf8mb4_unicode_ci COMMENT 'payByLinkUrl from JudoPay response (can be long)',
  `card_token` text COLLATE utf8mb4_unicode_ci COMMENT 'The obtained Card Token in the result of Successful CIT',
  `expiry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Session expiry time (24 hours from creation)',
  `status` enum('created','success','declined','refunded','expired','cancelled','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Each Consumer Reference should have only one active session. whether outcome is success, declined, refunded or expired.',
  `consent_given_at` timestamp NULL DEFAULT NULL COMMENT 'When customer ticked consent checkbox',
  `consent_ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer IP address at consent time',
  `consent_terms_version` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Version of terms accepted (e.g., v1.0-judopay-cit)',
  `consent_content_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA-256 hash of consent text shown to customer for audit trail',
  `sms_verification_sid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Twilio SMS SID linking to sms_messages.sid',
  `sms_verified_at` timestamp NULL DEFAULT NULL COMMENT 'When SMS code was successfully verified',
  `judopay_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'NOT SENSITIVE.Recent Full response from JudoPay session creation',
  `judopay_webhook_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Recent Webhook data from JudoPay',
  `judopay_session_status` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Recent Session status from JudoPay, if additional call made from get request to check recept / reference ',
  `status_score` int NOT NULL DEFAULT '0' COMMENT 'CIT need 2, MIT need 1.',
  `payment_completed_at` timestamp NULL DEFAULT NULL,
  `link_generated_at` timestamp NULL DEFAULT NULL,
  `customer_accessed_at` timestamp NULL DEFAULT NULL COMMENT 'UI Redirect to Judopay from Hostapplication',
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_cit_payment_sessions_judopay_payment_reference_unique` (`judopay_payment_reference`),
  KEY `judopay_cit_payment_sessions_subscription_id_foreign` (`subscription_id`),
  KEY `judopay_cit_payment_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `judopay_cit_payment_sessions_chk_1` CHECK (json_valid(`judopay_response`)),
  CONSTRAINT `judopay_cit_payment_sessions_chk_2` CHECK (json_valid(`judopay_webhook_data`)),
  CONSTRAINT `judopay_cit_payment_sessions_chk_3` CHECK (json_valid(`judopay_session_status`))
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_cit_payment_sessions');
    }
};
