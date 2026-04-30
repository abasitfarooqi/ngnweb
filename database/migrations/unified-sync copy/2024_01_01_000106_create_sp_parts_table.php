<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: connected target-only table
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('sp_parts'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `sp_parts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `part_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stock_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NOT IN STOCK',
  `price_gbp_inc_vat` decimal(10,2) NOT NULL DEFAULT '0.00',
  `global_stock` decimal(10,2) NOT NULL DEFAULT '0.00',
  `meta` json DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sp_parts_part_number_unique` (`part_number`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sp_parts');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
