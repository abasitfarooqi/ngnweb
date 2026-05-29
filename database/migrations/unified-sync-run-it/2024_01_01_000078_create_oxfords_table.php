<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('oxfords'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `oxfords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sku` text NOT NULL,
  `description` text DEFAULT NULL,
  `ean` text NOT NULL,
  `price` double(8,2) NOT NULL,
  `vat_price` double(8,2) NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `estimated_delivery` text DEFAULT NULL,
  `image_name` text NOT NULL,
  `vatable` text NOT NULL,
  `obsolete` text NOT NULL,
  `dead` text NOT NULL,
  `replacement_product` text DEFAULT NULL,
  `brand` text DEFAULT NULL,
  `extended_description` text DEFAULT NULL,
  `variation` text DEFAULT NULL,
  `date_added` text DEFAULT NULL,
  `pid` text DEFAULT NULL,
  `super_product_name` text DEFAULT NULL,
  `colour` text DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `category` text DEFAULT NULL,
  `model` text DEFAULT NULL,
  `category_id` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19978 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('oxfords');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
