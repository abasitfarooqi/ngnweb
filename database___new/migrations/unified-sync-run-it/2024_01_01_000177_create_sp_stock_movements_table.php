<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: connected target-only table
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('sp_stock_movements'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `sp_stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sp_part_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `in` decimal(10,2) NOT NULL DEFAULT '0.00',
  `out` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'adjustment',
  `user_id` bigint unsigned DEFAULT NULL,
  `ref_doc_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sp_stock_movements_branch_id_foreign` (`branch_id`),
  KEY `sp_stock_movements_user_id_foreign` (`user_id`),
  KEY `sp_stock_movements_sp_part_id_branch_id_index` (`sp_part_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sp_stock_movements');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
