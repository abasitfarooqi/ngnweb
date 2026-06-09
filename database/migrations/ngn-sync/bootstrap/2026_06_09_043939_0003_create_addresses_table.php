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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('addresses'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `addresses` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`street_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
`street_address_plus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`post_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
`city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
`phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`is_default` tinyint(1) NOT NULL DEFAULT '0',
`type` enum('billing','shipping') COLLATE utf8mb4_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
