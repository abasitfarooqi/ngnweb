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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('sessions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `sessions` (
`id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`user_id` bigint unsigned DEFAULT NULL,
`ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`user_agent` text COLLATE utf8mb4_unicode_ci,
`payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
`last_activity` int NOT NULL,
PRIMARY KEY (`id`),
KEY `sessions_user_id_index` (`user_id`),
KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
