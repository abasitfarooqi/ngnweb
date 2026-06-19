<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attribute_value_product_attribute')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_value_product_attribute` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_value_id` bigint unsigned DEFAULT NULL,
  `product_attribute_id` bigint unsigned NOT NULL,
  `product_custom_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `attribute_value_product_attribute_attribute_value_id_index` (`attribute_value_id`),
  KEY `attribute_value_product_attribute_product_attribute_id_index` (`product_attribute_id`),
  CONSTRAINT `attribute_value_product_attribute_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attribute_value_product_attribute_product_attribute_id_foreign` FOREIGN KEY (`product_attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
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
