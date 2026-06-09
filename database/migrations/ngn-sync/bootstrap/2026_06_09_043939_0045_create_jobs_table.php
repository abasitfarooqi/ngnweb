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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('jobs'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `jobs` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
`attempts` tinyint unsigned NOT NULL,
`reserved_at` int unsigned DEFAULT NULL,
`available_at` int unsigned NOT NULL,
`created_at` int unsigned NOT NULL,
PRIMARY KEY (`id`),
KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
