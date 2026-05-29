<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('booking_issuance_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_issuance_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_item_id` bigint(20) unsigned NOT NULL,
  `issued_by_user_id` bigint(20) unsigned NOT NULL,
  `current_mileage` int(11) NOT NULL,
  `is_video_recorded` tinyint(1) NOT NULL DEFAULT 0,
  `accessories_checked` tinyint(1) NOT NULL DEFAULT 0,
  `issuance_branch` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_insured` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `booking_issuance_items_issued_by_user_id_foreign` (`issued_by_user_id`),
  KEY `booking_issuance_items_booking_item_id_index` (`booking_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=696 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_issuance_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
