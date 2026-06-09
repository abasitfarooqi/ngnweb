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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('branches'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `branches` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`latitude` decimal(10,8) DEFAULT NULL,
`longitude` decimal(11,8) DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`opening_hours` text COLLATE utf8mb4_unicode_ci,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
