<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ngn_products'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) NOT NULL,
  `ean` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `variation` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `extended_description` text DEFAULT NULL,
  `colour` varchar(255) DEFAULT '',
  `pos_variant_id` varchar(255) DEFAULT NULL,
  `pos_product_id` varchar(255) DEFAULT NULL,
  `brand_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `normal_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pos_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pos_vat` decimal(10,2) NOT NULL DEFAULT 0.00,
  `global_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vatable` tinyint(1) NOT NULL DEFAULT 0,
  `is_oxford` tinyint(1) NOT NULL DEFAULT 0,
  `dead` tinyint(1) NOT NULL DEFAULT 0,
  `sorting_code` varchar(255) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` text NOT NULL,
  `is_ecommerce` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_products_sku_unique` (`sku`),
  KEY `ngn_products_brand_id_foreign` (`brand_id`),
  KEY `ngn_products_category_id_foreign` (`category_id`),
  KEY `ngn_products_model_id_foreign` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=105464 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_products');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
