<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('booking_invoices_backup_future_cleanup_2026_05_26'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_invoices_backup_future_cleanup_2026_05_26` (
  `id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `booking_id` bigint(20) unsigned NOT NULL,
  `invoice_date` date NOT NULL DEFAULT '2000-01-01',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_date` date DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `notified_at` datetime DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deposit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_whatsapp_sent` tinyint(1) DEFAULT 0,
  `whatsapp_last_reminder_sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_invoices_backup_future_cleanup_2026_05_26');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
