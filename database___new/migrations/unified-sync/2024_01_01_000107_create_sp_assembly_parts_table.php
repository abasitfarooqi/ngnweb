<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: connected target-only table
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('sp_assembly_parts'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `sp_assembly_parts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assembly_id` bigint unsigned NOT NULL,
  `part_id` bigint unsigned NOT NULL,
  `qty_used` int unsigned NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `note_override` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price_override` decimal(10,2) DEFAULT NULL,
  `stock_override` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sp_assembly_parts_assembly_id_part_id_unique` (`assembly_id`,`part_id`),
  KEY `sp_assembly_parts_part_id_foreign` (`part_id`),
  KEY `sp_assembly_parts_assembly_id_sort_order_index` (`assembly_id`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=2035 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sp_assembly_parts');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
