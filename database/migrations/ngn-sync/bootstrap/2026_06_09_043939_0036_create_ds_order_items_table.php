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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ds_order_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ds_order_items` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`ds_order_id` bigint unsigned NOT NULL,
`pickup_lat` decimal(10,8) NOT NULL,
`pickup_lon` decimal(10,8) NOT NULL,
`dropoff_lat` decimal(10,8) NOT NULL,
`dropoff_lon` decimal(10,8) NOT NULL,
`pickup_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Point of Pickup the actual asset Full Address.',
`pickup_postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Point of Pickup the actual asset postcode.',
`dropoff_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Point of Dropoff the actual asset Full Address.',
`distance` decimal(10,2) DEFAULT NULL COMMENT 'Total approx. Distance in miles',
`dropoff_postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Point of Dropoff the actual asset postcode.',
`vrm` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Vehicle Reg No.',
`moveable` tinyint(1) DEFAULT '0' COMMENT 'Is bike movebale or require lift-up to load and unload?',
`documents` text COLLATE utf8mb4_unicode_ci,
`keys` text COLLATE utf8mb4_unicode_ci,
`note` text COLLATE utf8mb4_unicode_ci,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ds_order_items_ds_order_id_foreign` (`ds_order_id`),
CONSTRAINT `ds_order_items_ds_order_id_foreign` FOREIGN KEY (`ds_order_id`) REFERENCES `ds_orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('ds_order_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
