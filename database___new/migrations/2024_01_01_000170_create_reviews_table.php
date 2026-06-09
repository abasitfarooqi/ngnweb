<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_recommended` tinyint(1) NOT NULL DEFAULT '0',
  `rating` int NOT NULL,
  `title` text COLLATE utf8mb4_general_ci,
  `content` text COLLATE utf8mb4_general_ci,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `reviewrateable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reviewrateable_id` bigint unsigned NOT NULL,
  `author_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_reviewrateable_type_reviewrateable_id_index` (`reviewrateable_type`,`reviewrateable_id`),
  KEY `reviews_author_type_author_id_index` (`author_type`,`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
