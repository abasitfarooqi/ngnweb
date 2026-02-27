<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_cit_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `passcode` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `subscription_id` bigint unsigned NOT NULL,
  `admin_form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Admin-entered form data for CIT session',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_accessed_at` timestamp NULL DEFAULT NULL COMMENT 'Last time customer accessed the authorization link',
  `access_ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer IP address when they first accessed the authorization link',
  `sms_requested_at` timestamp NULL DEFAULT NULL COMMENT 'When customer clicked "Send SMS Code" button',
  `sms_request_count` int NOT NULL DEFAULT '0' COMMENT 'Number of times SMS code was requested',
  `sms_sids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Array of SMS message SIDs sent for this authorization link',
  PRIMARY KEY (`id`),
  KEY `judopay_cit_accesses_customer_id_foreign` (`customer_id`),
  KEY `judopay_cit_accesses_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `judopay_cit_accesses_chk_1` CHECK (json_valid(`admin_form_data`)),
  CONSTRAINT `judopay_cit_accesses_chk_2` CHECK (json_valid(`sms_sids`))
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_cit_accesses');
    }
};
