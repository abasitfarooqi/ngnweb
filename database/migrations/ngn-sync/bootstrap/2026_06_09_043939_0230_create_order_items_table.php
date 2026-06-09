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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('order_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_items` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The product name at the moment of buying',
`sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`product_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`product_id` bigint unsigned NOT NULL,
`quantity` int NOT NULL,
`unit_price_amount` int NOT NULL,
`order_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `order_items_product_type_product_id_index` (`product_type`,`product_id`),
KEY `order_items_sku_index` (`sku`),
KEY `order_items_order_id_index` (`order_id`),
CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
