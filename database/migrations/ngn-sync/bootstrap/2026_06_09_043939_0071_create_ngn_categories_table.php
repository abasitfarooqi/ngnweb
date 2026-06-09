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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ngn_categories'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_categories` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`description` text COLLATE utf8mb4_unicode_ci NOT NULL,
`is_ecommerce` tinyint(1) NOT NULL DEFAULT '1',
`is_active` tinyint(1) NOT NULL DEFAULT '1',
`sort_order` int NOT NULL DEFAULT '0',
`meta_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`meta_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
`super_category_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `ngn_categories_name_unique` (`name`),
KEY `ngn_categories_super_category_id_foreign` (`super_category_id`),
CONSTRAINT `ngn_categories_super_category_id_foreign` FOREIGN KEY (`super_category_id`) REFERENCES `ngn_super_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_categories');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
