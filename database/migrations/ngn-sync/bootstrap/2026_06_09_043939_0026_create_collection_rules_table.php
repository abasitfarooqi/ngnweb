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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('collection_rules'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `collection_rules` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`rule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`operator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`collection_id` bigint unsigned NOT NULL,
PRIMARY KEY (`id`),
KEY `collection_rules_collection_id_index` (`collection_id`),
CONSTRAINT `collection_rules_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_rules');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
