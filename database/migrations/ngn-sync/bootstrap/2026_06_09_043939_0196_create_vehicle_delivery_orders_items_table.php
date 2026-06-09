<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('vehicle_delivery_orders_items'));
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
KEY `vehicle_delivery_orders_items_drop_branch_id_foreign` (`drop_branch_id`),
CONSTRAINT `vehicle_delivery_orders_items_drop_branch_id_foreign` FOREIGN KEY (`drop_branch_id`) REFERENCES `branches` (`id`),
CONSTRAINT `vehicle_delivery_orders_items_vehicle_delivery_order_id_foreign` FOREIGN KEY (`vehicle_delivery_order_id`) REFERENCES `vehicle_delivery_orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_orders_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
