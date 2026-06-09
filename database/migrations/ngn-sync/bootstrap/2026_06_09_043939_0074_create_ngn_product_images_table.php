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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ngn_product_images'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_product_images` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`product_id` bigint unsigned DEFAULT NULL,
`sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`image_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ngn_product_images_product_id_foreign` (`product_id`),
CONSTRAINT `ngn_product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ngn_products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_product_images');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
