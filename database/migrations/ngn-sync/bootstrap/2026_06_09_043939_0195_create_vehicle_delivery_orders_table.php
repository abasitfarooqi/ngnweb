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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('vehicle_delivery_orders'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_orders` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`quote_date` datetime NOT NULL,
`pickup_date` datetime NOT NULL,
`vrm` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`total_distance` decimal(8,2) NOT NULL,
`surcharge` decimal(8,2) NOT NULL,
`delivery_vehicle_type_id` bigint unsigned NOT NULL,
`branch_id` bigint unsigned NOT NULL,
`user_id` bigint unsigned NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `vehicle_delivery_orders_delivery_vehicle_type_id_foreign` (`delivery_vehicle_type_id`),
KEY `vehicle_delivery_orders_branch_id_foreign` (`branch_id`),
KEY `vehicle_delivery_orders_user_id_foreign` (`user_id`),
CONSTRAINT `vehicle_delivery_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
CONSTRAINT `vehicle_delivery_orders_delivery_vehicle_type_id_foreign` FOREIGN KEY (`delivery_vehicle_type_id`) REFERENCES `delivery_vehicle_types` (`id`),
CONSTRAINT `vehicle_delivery_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_orders');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
