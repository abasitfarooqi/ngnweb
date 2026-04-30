<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('renting_booking_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_booking_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `weekly_rent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `start_date` date NOT NULL DEFAULT '2000-01-01',
  `due_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_booking_items_booking_id_foreign` (`booking_id`),
  KEY `renting_booking_items_motorbike_id_foreign` (`motorbike_id`),
  KEY `renting_booking_items_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_booking_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
