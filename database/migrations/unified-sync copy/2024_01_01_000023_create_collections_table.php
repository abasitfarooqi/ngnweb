<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('collections'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `collections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `type` enum('manual','auto') NOT NULL,
  `sort` varchar(255) DEFAULT NULL,
  `match_conditions` enum('all','any') DEFAULT NULL,
  `published_at` datetime NOT NULL DEFAULT '2023-04-10 14:45:19',
  `seo_title` varchar(60) DEFAULT NULL,
  `seo_description` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collections_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
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
