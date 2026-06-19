<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_delivery_rates')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `base_fee` decimal(8,2) NOT NULL COMMENT 'Flat starting fee £20.00',
  `per_mile_fee` decimal(8,2) NOT NULL COMMENT 'Cost per mile beyond the base distance £1.50',
  `base_distance` decimal(8,2) NOT NULL COMMENT 'Distance covered by the base fee 5 miles',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
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
