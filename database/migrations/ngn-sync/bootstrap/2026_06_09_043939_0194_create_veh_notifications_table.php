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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('veh_notifications'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `veh_notifications` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`reg_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`notify_email` tinyint(1) NOT NULL DEFAULT '0',
`notify_phone` tinyint(1) NOT NULL DEFAULT '0',
`enable` tinyint NOT NULL DEFAULT '1',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('veh_notifications');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
