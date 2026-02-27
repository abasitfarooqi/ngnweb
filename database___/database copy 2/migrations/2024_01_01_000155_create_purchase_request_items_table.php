<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `purchase_request_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pr_id` bigint unsigned NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chassis_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `part_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `part_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_one` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_two` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brand_name_id` bigint unsigned NOT NULL,
  `bike_model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_request_items_pr_id_foreign` (`pr_id`),
  KEY `purchase_request_items_created_by_foreign` (`created_by`),
  KEY `purchase_request_items_brand_name_id_foreign` (`brand_name_id`),
  KEY `purchase_request_items_bike_model_id_foreign` (`bike_model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};
