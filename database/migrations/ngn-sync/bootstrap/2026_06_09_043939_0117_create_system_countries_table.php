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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('system_countries'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `system_countries` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`name_official` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`cca2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`cca3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`flag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`latitude` decimal(10,8) NOT NULL,
`longitude` decimal(11,8) NOT NULL,
`currencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `system_countries_chk_1` CHECK (json_valid(`currencies`))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('system_countries');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
