<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ngn_product_images'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `image_url` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_product_images_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=109260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
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
