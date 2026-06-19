<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ec_order_items')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `item_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'catalogue',
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the product at the time of the order',
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SKU of the product at the time of the order',
  `part_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sp_part_id` bigint unsigned DEFAULT NULL,
  `sp_assembly_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Quantity of the product in the order',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Unit price of the product at the time of the order',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total price of the product at the time of the order',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Discount amount applied to the product at the time of the order',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tax amount applied to the product at the time of the order',
  `line_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Final total after shipping, tax and discounts',
  `source_meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ec_order_items_order_id_foreign` (`order_id`),
  KEY `ec_order_items_product_id_foreign` (`product_id`),
  KEY `ec_order_items_item_type_index` (`item_type`),
  KEY `ec_order_items_part_number_index` (`part_number`),
  KEY `ec_order_items_sp_part_id_index` (`sp_part_id`),
  KEY `ec_order_items_sp_assembly_id_index` (`sp_assembly_id`),
  CONSTRAINT `ec_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `ec_orders` (`id`),
  CONSTRAINT `ec_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ngn_products` (`id`) ON DELETE SET NULL
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
