<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the product at the time of the order',
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SKU of the product at the time of the order',
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Quantity of the product in the order',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Unit price of the product at the time of the order',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total price of the product at the time of the order',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Discount amount applied to the product at the time of the order',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tax amount applied to the product at the time of the order',
  `line_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Final total after shipping, tax and discounts',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ec_order_items_order_id_foreign` (`order_id`),
  KEY `ec_order_items_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_order_items');
    }
};
