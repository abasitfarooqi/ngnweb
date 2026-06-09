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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('categories'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `categories` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`description` longtext COLLATE utf8mb4_unicode_ci,
`position` smallint unsigned NOT NULL DEFAULT '0',
`is_enabled` tinyint(1) NOT NULL DEFAULT '0',
`seo_title` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`seo_description` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`parent_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `categories_slug_unique` (`slug`),
KEY `categories_parent_id_index` (`parent_id`),
CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
