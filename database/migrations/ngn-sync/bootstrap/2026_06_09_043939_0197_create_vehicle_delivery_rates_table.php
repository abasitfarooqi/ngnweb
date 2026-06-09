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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('vehicle_delivery_rates'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_rates` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`base_fee` decimal(8,2) NOT NULL COMMENT 'Flat starting fee £20.00',
`per_mile_fee` decimal(8,2) NOT NULL COMMENT 'Cost per mile beyond the base distance £1.50',
`base_distance` decimal(8,2) NOT NULL COMMENT 'Distance covered by the base fee 5 miles',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_rates');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
