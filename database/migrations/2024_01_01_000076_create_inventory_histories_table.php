<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `inventory_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stockable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stockable_id` bigint unsigned NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `old_quantity` int NOT NULL DEFAULT '0',
  `event` text COLLATE utf8mb4_general_ci,
  `description` text COLLATE utf8mb4_general_ci,
  `inventory_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_histories_stockable_type_stockable_id_index` (`stockable_type`,`stockable_id`),
  KEY `inventory_histories_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `inventory_histories_inventory_id_index` (`inventory_id`),
  KEY `inventory_histories_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};
