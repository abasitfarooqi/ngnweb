<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('product_has_relations'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `product_has_relations` (
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `productable_type` varchar(255) NOT NULL,
  `productable_id` bigint(20) unsigned NOT NULL,
  `stock_id` bigint(20) unsigned NOT NULL,
  KEY `product_has_relations_productable_type_productable_id_index` (`productable_type`,`productable_id`),
  KEY `product_has_relations_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('product_has_relations');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
