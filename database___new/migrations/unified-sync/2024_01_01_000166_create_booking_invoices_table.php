<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('booking_invoices'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `invoice_date` date NOT NULL DEFAULT '2000-01-01',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_date` date DEFAULT NULL,
  `state` varchar(255) NOT NULL DEFAULT 'DRAFT',
  `notes` text DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `notified_at` datetime DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deposit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_whatsapp_sent` tinyint(1) DEFAULT 0,
  `whatsapp_last_reminder_sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_invoices_booking_id_foreign` (`booking_id`),
  KEY `booking_invoices_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6333 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_invoices');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
