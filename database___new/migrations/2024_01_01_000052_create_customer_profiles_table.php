<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_auth_id` bigint unsigned NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  `license_issuance_authority` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_issuance_date` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'United Kingdom',
  `emergency_contact` json DEFAULT NULL,
  `preferred_branch_id` bigint unsigned DEFAULT NULL,
  `verification_status` enum('draft','submitted','verified','expired','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_expires_at` timestamp NULL DEFAULT NULL,
  `locked_fields` json DEFAULT NULL,
  `reputation_note` text COLLATE utf8mb4_unicode_ci,
  `rating` int DEFAULT '0',
  `is_register` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_profiles_preferred_branch_id_foreign` (`preferred_branch_id`),
  KEY `customer_profiles_verification_status_index` (`verification_status`),
  KEY `customer_profiles_customer_auth_id_index` (`customer_auth_id`),
  KEY `customer_profiles_license_number_index` (`license_number`),
  CONSTRAINT `customer_profiles_customer_auth_id_foreign` FOREIGN KEY (`customer_auth_id`) REFERENCES `customer_auths` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_profiles_preferred_branch_id_foreign` FOREIGN KEY (`preferred_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
