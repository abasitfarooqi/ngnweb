<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `product_id` bigint unsigned NOT NULL,
  `in` decimal(10,2) NOT NULL DEFAULT '0.00',
  `out` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transaction_type',
  `user_id` bigint unsigned NOT NULL,
  `ref_doc_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_stock_movements_branch_id_foreign` (`branch_id`),
  KEY `ngn_stock_movements_product_id_foreign` (`product_id`),
  KEY `ngn_stock_movements_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9492 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_stock_movements');
    }
};
