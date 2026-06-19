<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_delivery_orders')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_date` datetime NOT NULL,
  `pickup_date` datetime NOT NULL,
  `vrm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_distance` decimal(8,2) NOT NULL,
  `surcharge` decimal(8,2) NOT NULL,
  `delivery_vehicle_type_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_delivery_orders_delivery_vehicle_type_id_foreign` (`delivery_vehicle_type_id`),
  KEY `vehicle_delivery_orders_branch_id_foreign` (`branch_id`),
  KEY `vehicle_delivery_orders_user_id_foreign` (`user_id`),
  CONSTRAINT `vehicle_delivery_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `vehicle_delivery_orders_delivery_vehicle_type_id_foreign` FOREIGN KEY (`delivery_vehicle_type_id`) REFERENCES `delivery_vehicle_types` (`id`),
  CONSTRAINT `vehicle_delivery_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
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
