<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('vehicle_estimators'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_estimators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referer_id` varchar(255) DEFAULT NULL,
  `make` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `vrm` varchar(255) DEFAULT NULL,
  `engine_size` int(11) DEFAULT NULL,
  `mileage` int(11) DEFAULT NULL,
  `vehicle_year` varchar(255) DEFAULT NULL,
  `condition` int(11) DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `calculated_value` decimal(10,2) DEFAULT NULL,
  `like` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_estimators');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
