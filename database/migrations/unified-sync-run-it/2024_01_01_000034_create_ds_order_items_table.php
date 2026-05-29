<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ds_order_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ds_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ds_order_id` bigint(20) unsigned NOT NULL,
  `pickup_lat` decimal(10,8) NOT NULL,
  `pickup_lon` decimal(10,8) NOT NULL,
  `dropoff_lat` decimal(10,8) NOT NULL,
  `dropoff_lon` decimal(10,8) NOT NULL,
  `pickup_address` varchar(255) NOT NULL DEFAULT '' COMMENT 'Point of Pickup the actual asset Full Address.',
  `pickup_postcode` varchar(255) NOT NULL DEFAULT '' COMMENT 'Point of Pickup the actual asset postcode.',
  `dropoff_address` varchar(255) NOT NULL DEFAULT '' COMMENT 'Point of Dropoff the actual asset Full Address.',
  `distance` decimal(10,2) DEFAULT NULL COMMENT 'Total approx. Distance in miles',
  `dropoff_postcode` varchar(255) NOT NULL DEFAULT '' COMMENT 'Point of Dropoff the actual asset postcode.',
  `vrm` varchar(255) NOT NULL DEFAULT '' COMMENT 'Vehicle Reg No.',
  `moveable` tinyint(1) DEFAULT 0 COMMENT 'Is bike movebale or require lift-up to load and unload?',
  `documents` text,
  `keys` text,
  `note` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ds_order_items_ds_order_id_foreign` (`ds_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
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
