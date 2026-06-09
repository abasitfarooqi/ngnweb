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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('contacts'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `contacts` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`email` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`subject` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`phone` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`message` text COLLATE utf8mb4_unicode_ci,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`reg_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=705 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
