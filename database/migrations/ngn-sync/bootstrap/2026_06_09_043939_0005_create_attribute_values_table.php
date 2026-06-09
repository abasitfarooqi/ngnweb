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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('attribute_values'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_values` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
`key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`position` smallint unsigned DEFAULT '1',
`attribute_id` bigint unsigned NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `attribute_values_key_unique` (`key`),
KEY `attribute_values_attribute_id_index` (`attribute_id`),
CONSTRAINT `attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
