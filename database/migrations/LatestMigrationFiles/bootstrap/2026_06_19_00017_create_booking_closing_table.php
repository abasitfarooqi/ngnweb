<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_closing')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_closing` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `notice_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notice_checked` tinyint(1) NOT NULL DEFAULT '0',
  `collect_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `collect_date` date DEFAULT NULL,
  `collect_time` time DEFAULT NULL,
  `collect_checked` tinyint(1) NOT NULL DEFAULT '0',
  `damages_checked` tinyint(1) NOT NULL DEFAULT '0',
  `pcn_checked` tinyint(1) NOT NULL DEFAULT '0',
  `pending_checked` tinyint(1) NOT NULL DEFAULT '0',
  `deposit_checked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deposit_refunded_at` timestamp NULL DEFAULT NULL,
  `deposit_refund_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deposit_refund_proof_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deposit_refund_proof_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deposit_refund_user_id` bigint unsigned DEFAULT NULL,
  `deposit_refund_send_email` tinyint(1) NOT NULL DEFAULT '0',
  `deposit_refund_email_sent_at` timestamp NULL DEFAULT NULL,
  `collect_proceeded_anyway_user_id` bigint unsigned DEFAULT NULL,
  `collect_proceeded_anyway_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_closing_booking_id_foreign` (`booking_id`),
  KEY `booking_closing_deposit_refund_user_id_foreign` (`deposit_refund_user_id`),
  KEY `booking_closing_collect_proceeded_anyway_user_id_foreign` (`collect_proceeded_anyway_user_id`),
  CONSTRAINT `booking_closing_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`),
  CONSTRAINT `booking_closing_collect_proceeded_anyway_user_id_foreign` FOREIGN KEY (`collect_proceeded_anyway_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `booking_closing_deposit_refund_user_id_foreign` FOREIGN KEY (`deposit_refund_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
