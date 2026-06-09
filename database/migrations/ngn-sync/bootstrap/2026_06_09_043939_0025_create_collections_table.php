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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('collections'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `collections` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`description` longtext COLLATE utf8mb4_unicode_ci,
`type` enum('manual','auto') COLLATE utf8mb4_unicode_ci NOT NULL,
`sort` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`match_conditions` enum('all','any') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`published_at` datetime NOT NULL DEFAULT '2023-04-10 14:45:19',
`seo_title` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`seo_description` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `collections_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
