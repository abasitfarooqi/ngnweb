<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('renting_pricings')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_pricings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `iscurrent` tinyint(1) NOT NULL DEFAULT '1',
  `weekly_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `minimum_deposit` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_pricings_user_id_foreign` (`user_id`),
  CONSTRAINT `renting_pricings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
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
