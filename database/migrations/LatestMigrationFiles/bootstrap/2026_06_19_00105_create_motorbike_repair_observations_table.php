<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('motorbike_repair_observations')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_repair_observations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_repair_id` bigint unsigned NOT NULL,
  `observation_description` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_repair_observations_motorbike_repair_id_foreign` (`motorbike_repair_id`),
  CONSTRAINT `motorbike_repair_observations_motorbike_repair_id_foreign` FOREIGN KEY (`motorbike_repair_id`) REFERENCES `motorbikes_repair` (`id`)
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
