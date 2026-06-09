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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('oxfords'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `oxfords` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`sku` text COLLATE utf8mb4_unicode_ci NOT NULL,
`description` text COLLATE utf8mb4_unicode_ci,
`ean` text COLLATE utf8mb4_unicode_ci NOT NULL,
`price` double(8,2) NOT NULL,
`vat_price` double(8,2) NOT NULL,
`stock` int DEFAULT NULL,
`estimated_delivery` text COLLATE utf8mb4_unicode_ci,
`image_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
`vatable` text COLLATE utf8mb4_unicode_ci NOT NULL,
`obsolete` text COLLATE utf8mb4_unicode_ci NOT NULL,
`dead` text COLLATE utf8mb4_unicode_ci NOT NULL,
`replacement_product` text COLLATE utf8mb4_unicode_ci,
`brand` text COLLATE utf8mb4_unicode_ci,
`extended_description` text COLLATE utf8mb4_unicode_ci,
`variation` text COLLATE utf8mb4_unicode_ci,
`date_added` text COLLATE utf8mb4_unicode_ci,
`pid` text COLLATE utf8mb4_unicode_ci,
`super_product_name` text COLLATE utf8mb4_unicode_ci,
`colour` text COLLATE utf8mb4_unicode_ci,
`image_url` text COLLATE utf8mb4_unicode_ci,
`category` text COLLATE utf8mb4_unicode_ci,
`model` text COLLATE utf8mb4_unicode_ci,
`category_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`quantity` int DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19978 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('oxfords');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
