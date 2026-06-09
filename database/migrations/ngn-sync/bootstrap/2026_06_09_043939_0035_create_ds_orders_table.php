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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ds_orders'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ds_orders` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`pick_up_datetime` datetime NOT NULL DEFAULT '2024-12-25 14:53:16' COMMENT 'Unlike Timestamp. It is a date of time pickup',
`full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Name of person who book order',
`phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Phone Number of person who book order',
`address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Person Address who order. It is important regardless pickup address and drop-off address.',
`postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Postcode of person who proceed the order',
`note` text COLLATE utf8mb4_unicode_ci,
`proceed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'It remain false unless customer pay.',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('ds_orders');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
