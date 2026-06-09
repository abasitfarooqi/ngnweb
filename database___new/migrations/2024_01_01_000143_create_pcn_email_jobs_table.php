<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_email_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `template_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `case_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `force_stop` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pcn_email_jobs_motorbike_id_foreign` (`motorbike_id`),
  KEY `pcn_email_jobs_customer_id_foreign` (`customer_id`),
  KEY `pcn_email_jobs_case_id_foreign` (`case_id`),
  KEY `pcn_email_jobs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=634 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('pcn_email_jobs');
    }
};
