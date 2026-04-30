<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_maintenance_logs'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_maintenance_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `booking_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `serviced_at` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_maintenance_logs_motorbike_id_foreign` (`motorbike_id`),
  KEY `motorbike_maintenance_logs_booking_id_foreign` (`booking_id`),
  KEY `motorbike_maintenance_logs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_maintenance_logs');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
