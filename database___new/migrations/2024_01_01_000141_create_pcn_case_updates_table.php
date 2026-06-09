<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_case_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `case_id` bigint unsigned NOT NULL,
  `update_date` datetime NOT NULL,
  `is_appealed` tinyint(1) DEFAULT '0',
  `is_paid_by_owner` tinyint(1) DEFAULT '0',
  `is_paid_by_keeper` tinyint(1) DEFAULT '0',
  `additional_fee` decimal(10,2) DEFAULT '0.00',
  `picture_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_transferred` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_cancled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pcn_case_updates_user_id_foreign` (`user_id`),
  KEY `pcn_case_updates_case_id_foreign` (`case_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5530 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('pcn_case_updates');
    }
};
