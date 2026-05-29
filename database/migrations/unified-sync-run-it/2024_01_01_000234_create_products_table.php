<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('products'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `security_stock` int(11) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `old_price_amount` int(11) DEFAULT NULL,
  `price_amount` int(11) DEFAULT NULL,
  `cost_amount` int(11) DEFAULT NULL,
  `type` enum('deliverable','downloadable') DEFAULT NULL,
  `backorder` tinyint(1) NOT NULL DEFAULT 0,
  `requires_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT '2023-04-10 14:45:19',
  `seo_title` varchar(60) DEFAULT NULL,
  `seo_description` varchar(160) DEFAULT NULL,
  `weight_value` decimal(10,5) unsigned DEFAULT 0.00000,
  `weight_unit` varchar(255) NOT NULL DEFAULT 'kg',
  `height_value` decimal(10,5) unsigned DEFAULT 0.00000,
  `height_unit` varchar(255) NOT NULL DEFAULT 'cm',
  `width_value` decimal(10,5) unsigned DEFAULT 0.00000,
  `width_unit` varchar(255) NOT NULL DEFAULT 'cm',
  `depth_value` decimal(10,5) unsigned DEFAULT 0.00000,
  `depth_unit` varchar(255) NOT NULL DEFAULT 'cm',
  `volume_value` decimal(10,5) unsigned DEFAULT 0.00000,
  `volume_unit` varchar(255) NOT NULL DEFAULT 'l',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `stock_id` bigint(20) unsigned DEFAULT NULL,
  `image` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_parent_id_index` (`parent_id`),
  KEY `products_brand_id_index` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
