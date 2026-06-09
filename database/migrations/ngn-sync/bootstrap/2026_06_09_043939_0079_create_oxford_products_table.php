<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('oxford_products'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `oxford_products` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`ean` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`rrp_less_vat` decimal(8,2) NOT NULL DEFAULT '0.00',
`rrp_inc_vat` decimal(8,2) NOT NULL DEFAULT '0.00',
`stock` int NOT NULL DEFAULT '0',
`catford_stock` int DEFAULT '0',
`estimated_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`image_file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`vatable` tinyint(1) DEFAULT NULL,
`obsolete` tinyint(1) DEFAULT NULL,
`category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`supplier_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`cost_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`extended_description` text COLLATE utf8mb4_unicode_ci,
`variation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`date_added` timestamp NULL DEFAULT NULL,
`super_product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`colour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`image_url` text COLLATE utf8mb4_unicode_ci,
`model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`dead` tinyint(1) DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `oxford_products_sku_unique` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=25678 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
