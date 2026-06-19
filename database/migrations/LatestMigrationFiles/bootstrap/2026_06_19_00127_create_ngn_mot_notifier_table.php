<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ngn_mot_notifier')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_mot_notifier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned DEFAULT NULL,
  `motorbike_reg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_due_date` date DEFAULT NULL,
  `tax_due_date` date DEFAULT NULL,
  `insurance_due_date` date DEFAULT NULL,
  `mot_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_contact` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_notify_email` tinyint(1) NOT NULL DEFAULT '1',
  `mot_notify_phone` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `mot_email_sent_expired` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_phone_sent` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_whatsapp_sent` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_notified_30` tinyint(1) NOT NULL DEFAULT '0',
  `mot_email_sent_30` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_notified_10` tinyint(1) NOT NULL DEFAULT '0',
  `mot_email_sent_10` tinyint(1) NOT NULL DEFAULT '0',
  `mot_last_email_notification_date` date DEFAULT NULL,
  `mot_last_phone_notification_date` date DEFAULT NULL,
  `mot_last_whatsapp_notification_date` date DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_mot_notifier_motorbike_id_foreign` (`motorbike_id`)
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
