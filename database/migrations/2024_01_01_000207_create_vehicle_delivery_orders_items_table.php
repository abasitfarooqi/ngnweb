<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_orders_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_delivery_order_id` bigint unsigned NOT NULL,
  `pickup_point_coordinates_lat` decimal(10,7) NOT NULL,
  `pickup_point_coordinates_lon` decimal(10,7) NOT NULL,
  `drop_branch_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_delivery_orders_items_vehicle_delivery_order_id_foreign` (`vehicle_delivery_order_id`),
  KEY `vehicle_delivery_orders_items_drop_branch_id_foreign` (`drop_branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_orders_items');
    }
};
