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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('payment_methods'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `payment_methods` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`description` text COLLATE utf8mb4_unicode_ci,
`instructions` text COLLATE utf8mb4_unicode_ci,
`is_enabled` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
UNIQUE KEY `payment_methods_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
