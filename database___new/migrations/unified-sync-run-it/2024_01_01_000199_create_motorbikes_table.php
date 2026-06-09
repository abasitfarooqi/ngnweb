<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbikes'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbikes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_profile_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `is_ebike` tinyint(1) NOT NULL DEFAULT 0,
  `vin_number` varchar(255) NOT NULL,
  `make` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `year` year(4) NOT NULL,
  `engine` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `co2_emissions` varchar(255) DEFAULT NULL,
  `fuel_type` varchar(255) DEFAULT NULL,
  `marked_for_export` tinyint(1) NOT NULL DEFAULT 0,
  `type_approval` varchar(255) DEFAULT NULL,
  `wheel_plan` varchar(255) DEFAULT NULL,
  `month_of_first_registration` date DEFAULT NULL,
  `reg_no` varchar(255) DEFAULT NULL,
  `date_of_last_v5c_issuance` date DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `motorbikes_vin_number_unique` (`vin_number`),
  KEY `motorbikes_vehicle_profile_id_foreign` (`vehicle_profile_id`),
  KEY `motorbikes_branch_id_foreign` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2358 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
