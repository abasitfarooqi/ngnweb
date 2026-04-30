<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('brands'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `position` smallint(5) unsigned NOT NULL DEFAULT 0,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `seo_title` varchar(60) DEFAULT NULL,
  `seo_description` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
