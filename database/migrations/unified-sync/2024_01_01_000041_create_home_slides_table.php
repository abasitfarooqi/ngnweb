<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('home_slides'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `home_slides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(125) DEFAULT NULL,
  `short_title` varchar(125) DEFAULT NULL,
  `home_slide` varchar(125) DEFAULT NULL,
  `video_url` varchar(125) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('home_slides');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
