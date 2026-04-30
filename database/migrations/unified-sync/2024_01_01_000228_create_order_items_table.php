<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('order_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'The product name at the moment of buying',
  `sku` varchar(255) DEFAULT NULL,
  `product_type` varchar(255) NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price_amount` int(11) NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_product_type_product_id_index` (`product_type`,`product_id`),
  KEY `order_items_sku_index` (`sku`),
  KEY `order_items_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
