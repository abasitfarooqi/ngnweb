<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('survey_email_campaigns')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `survey_email_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ngn_survey_id` bigint unsigned NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_email` tinyint(1) NOT NULL DEFAULT '0',
  `send_phone` tinyint(1) NOT NULL DEFAULT '0',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `last_email_sent_datetime` timestamp NULL DEFAULT NULL,
  `last_sms_sent_datetime` timestamp NULL DEFAULT NULL,
  `is_email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_sms_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_whatsapp_sent` tinyint(1) NOT NULL DEFAULT '0',
  `url_whatsapp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_whatsapp_sent_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_email_campaigns_ngn_survey_id_foreign` (`ngn_survey_id`),
  CONSTRAINT `survey_email_campaigns_ngn_survey_id_foreign` FOREIGN KEY (`ngn_survey_id`) REFERENCES `ngn_surveys` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
