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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ip_restrictions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ip_restrictions` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`status` enum('allowed','blocked') COLLATE utf8mb4_unicode_ci NOT NULL,
`restriction_type` enum('admin_only','full_site') COLLATE utf8mb4_unicode_ci NOT NULL,
`label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`user_id` bigint DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_restrictions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
