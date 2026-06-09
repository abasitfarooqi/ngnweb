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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('rentals'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `rentals` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`make` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
`model` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
`engine` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
`year` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
`colour` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
`user_id` bigint DEFAULT NULL,
`signature` blob,
`motorcycle_id` bigint DEFAULT NULL,
`registration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`deposit` decimal(8,2) DEFAULT NULL,
`price` decimal(8,2) DEFAULT NULL,
`created_at` date DEFAULT NULL,
`updated_at` date DEFAULT NULL,
`auth_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`deleted_at` datetime DEFAULT NULL,
`deleted_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
