<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('delivery_vehicle_types'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `delivery_vehicle_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name of the bike type (e.g., "Standard", "Mid-Range")',
  `cc_range` varchar(255) NOT NULL COMMENT 'Engine range (e.g., "0-125cc", "126-600cc", "601cc+")',
  `additional_fee` decimal(8,2) NOT NULL COMMENT 'Extra fee for this type',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_vehicle_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_vehicle_types');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
