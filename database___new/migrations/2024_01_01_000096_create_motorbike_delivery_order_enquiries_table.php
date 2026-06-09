<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_delivery_order_enquiries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_postcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dropoff_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dropoff_postcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vrm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moveable` tinyint(1) DEFAULT NULL,
  `documents` tinyint(1) DEFAULT NULL,
  `keys` tinyint(1) DEFAULT NULL,
  `pick_up_datetime` datetime DEFAULT NULL,
  `distance` double(8,2) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_cost` double(8,2) DEFAULT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type_id` bigint unsigned DEFAULT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `is_dealt` tinyint(1) DEFAULT '0',
  `dealt_by_user_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_delivery_order_enquiries');
    }
};
