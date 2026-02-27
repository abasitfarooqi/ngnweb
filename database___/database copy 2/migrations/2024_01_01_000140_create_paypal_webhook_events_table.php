<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `paypal_webhook_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint unsigned NOT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `transmission_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transmission_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transmission_sig` text COLLATE utf8mb4_unicode_ci,
  `auth_algo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paypal_webhook_events_transmission_id_unique` (`transmission_id`),
  KEY `paypal_webhook_events_payment_id_foreign` (`payment_id`),
  CONSTRAINT `paypal_webhook_events_chk_1` CHECK (json_valid(`resource`)),
  CONSTRAINT `paypal_webhook_events_chk_2` CHECK (json_valid(`payload`))
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_webhook_events');
    }
};
