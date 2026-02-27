<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `footers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_general_ci,
  `adress` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `facebook` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `twitter` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `copyright` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('footers');
    }
};
