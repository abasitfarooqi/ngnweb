<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('attribute_value_product_attribute'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_value_product_attribute` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_value_id` bigint(20) unsigned DEFAULT NULL,
  `product_attribute_id` bigint(20) unsigned NOT NULL,
  `product_custom_value` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_value_product_attribute_attribute_value_id_index` (`attribute_value_id`),
  KEY `attribute_value_product_attribute_product_attribute_id_index` (`product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_value_product_attribute');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
