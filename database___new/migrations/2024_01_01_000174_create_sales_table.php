<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `brand_name` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `generic_name` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `orginal_price` double DEFAULT NULL,
  `sell_price` double DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_user_id_foreign` (`user_id`),
  KEY `sales_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
