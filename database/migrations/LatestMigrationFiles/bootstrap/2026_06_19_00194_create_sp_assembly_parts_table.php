<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sp_assembly_parts')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `sp_assembly_parts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assembly_id` bigint unsigned NOT NULL,
  `part_id` bigint unsigned NOT NULL,
  `qty_used` int unsigned NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `note_override` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price_override` decimal(10,2) DEFAULT NULL,
  `stock_override` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sp_assembly_parts_assembly_id_part_id_unique` (`assembly_id`,`part_id`),
  KEY `sp_assembly_parts_part_id_foreign` (`part_id`),
  KEY `sp_assembly_parts_assembly_id_sort_order_index` (`assembly_id`,`sort_order`),
  CONSTRAINT `sp_assembly_parts_assembly_id_foreign` FOREIGN KEY (`assembly_id`) REFERENCES `sp_assemblies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sp_assembly_parts_part_id_foreign` FOREIGN KEY (`part_id`) REFERENCES `sp_parts` (`id`) ON DELETE CASCADE
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
