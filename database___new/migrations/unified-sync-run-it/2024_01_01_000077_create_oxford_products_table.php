<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('oxford_products'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `oxford_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ean` varchar(255) DEFAULT NULL,
  `rrp_less_vat` decimal(8,2) NOT NULL DEFAULT 0.00,
  `rrp_inc_vat` decimal(8,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `catford_stock` int(11) DEFAULT 0,
  `estimated_delivery` varchar(255) DEFAULT NULL,
  `image_file_name` varchar(255) DEFAULT NULL,
  `vatable` tinyint(1) DEFAULT NULL,
  `obsolete` tinyint(1) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `supplier_code` varchar(255) DEFAULT NULL,
  `cost_price` varchar(255) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `extended_description` text DEFAULT NULL,
  `variation` varchar(255) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT NULL,
  `super_product_name` varchar(255) DEFAULT NULL,
  `colour` varchar(255) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dead` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oxford_products_sku_unique` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=25678 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('oxford_products');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
