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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('attribute_value_product_attribute'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_value_product_attribute` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`attribute_value_id` bigint unsigned DEFAULT NULL,
`product_attribute_id` bigint unsigned NOT NULL,
`product_custom_value` longtext COLLATE utf8mb4_unicode_ci,
PRIMARY KEY (`id`),
KEY `attribute_value_product_attribute_attribute_value_id_index` (`attribute_value_id`),
KEY `attribute_value_product_attribute_product_attribute_id_index` (`product_attribute_id`),
CONSTRAINT `attribute_value_product_attribute_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE SET NULL,
CONSTRAINT `attribute_value_product_attribute_product_attribute_id_foreign` FOREIGN KEY (`product_attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
