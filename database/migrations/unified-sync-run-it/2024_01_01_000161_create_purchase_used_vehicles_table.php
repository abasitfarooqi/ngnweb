<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('purchase_used_vehicles'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `purchase_used_vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_date` varchar(255) NOT NULL DEFAULT '',
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `make` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `colour` varchar(255) NOT NULL,
  `fuel_type` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `reg_no` varchar(255) NOT NULL,
  `current_mileage` int(11) NOT NULL DEFAULT 0,
  `vin` varchar(255) NOT NULL,
  `engine_number` varchar(255) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `deposit` decimal(8,2) NOT NULL DEFAULT 0.00,
  `outstanding` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_to_pay` decimal(8,2) NOT NULL DEFAULT 0.00,
  `account_name` varchar(255) DEFAULT NULL,
  `sort_code` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_used_vehicles_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_used_vehicles');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
