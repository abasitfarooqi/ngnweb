<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sp_stock_movements')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
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
  KEY `sp_stock_movements_sp_part_id_branch_id_index` (`sp_part_id`,`branch_id`),
  CONSTRAINT `sp_stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sp_stock_movements_sp_part_id_foreign` FOREIGN KEY (`sp_part_id`) REFERENCES `sp_parts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sp_stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
