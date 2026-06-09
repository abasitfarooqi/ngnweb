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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('sales'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `sales` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned NOT NULL,
`product_id` bigint unsigned NOT NULL,
`brand_name` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`generic_name` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`category` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`orginal_price` double DEFAULT NULL,
`sell_price` double DEFAULT NULL,
`quantity` int DEFAULT NULL,
`profit` double DEFAULT NULL,
`total` double DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `sales_user_id_foreign` (`user_id`),
KEY `sales_product_id_foreign` (`product_id`),
CONSTRAINT `sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users-old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
