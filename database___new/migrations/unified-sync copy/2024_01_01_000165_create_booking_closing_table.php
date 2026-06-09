<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('booking_closing'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_closing` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `notice_details` text DEFAULT NULL,
  `notice_checked` tinyint(1) NOT NULL DEFAULT 0,
  `collect_details` text DEFAULT NULL,
  `collect_date` date DEFAULT NULL,
  `collect_time` time DEFAULT NULL,
  `collect_checked` tinyint(1) NOT NULL DEFAULT 0,
  `damages_checked` tinyint(1) NOT NULL DEFAULT 0,
  `pcn_checked` tinyint(1) NOT NULL DEFAULT 0,
  `pending_checked` tinyint(1) NOT NULL DEFAULT 0,
  `deposit_checked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deposit_refunded_at` timestamp NULL DEFAULT NULL,
  `deposit_refund_method` varchar(255) DEFAULT NULL,
  `deposit_refund_proof_path` varchar(255) DEFAULT NULL,
  `deposit_refund_proof_reference` varchar(255) DEFAULT NULL,
  `deposit_refund_user_id` bigint(20) unsigned DEFAULT NULL,
  `deposit_refund_send_email` tinyint(1) NOT NULL DEFAULT 0,
  `deposit_refund_email_sent_at` timestamp NULL DEFAULT NULL,
  `collect_proceeded_anyway_user_id` bigint(20) unsigned DEFAULT NULL,
  `collect_proceeded_anyway_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_closing_booking_id_foreign` (`booking_id`),
  KEY `booking_closing_deposit_refund_user_id_foreign` (`deposit_refund_user_id`),
  KEY `booking_closing_collect_proceeded_anyway_user_id_foreign` (`collect_proceeded_anyway_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_closing');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
