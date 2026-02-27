<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `portfolios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `portfolio_name` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `portfolio_title` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `portfolio_image` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `portfolio_description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
