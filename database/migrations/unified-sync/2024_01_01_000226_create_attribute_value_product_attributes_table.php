<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('attribute_value_product_attributes'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_value_product_attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_value_id` bigint(20) unsigned DEFAULT NULL,
  `product_attribute_id` bigint(20) unsigned NOT NULL,
  `product_custom_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_value_product_attributes_attribute_value_id_foreign` (`attribute_value_id`),
  KEY `attribute_value_product_attributes_product_attribute_id_foreign` (`product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_value_product_attributes');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
