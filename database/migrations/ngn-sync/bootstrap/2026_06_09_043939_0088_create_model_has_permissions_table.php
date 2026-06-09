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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('model_has_permissions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `model_has_permissions` (
`permission_id` bigint unsigned NOT NULL,
`model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`model_id` bigint unsigned NOT NULL,
PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
