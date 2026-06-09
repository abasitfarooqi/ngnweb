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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('repair_update_service'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `repair_update_service` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`update_id` bigint unsigned NOT NULL,
`service_id` bigint unsigned NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `repair_update_service_update_id_foreign` (`update_id`),
KEY `repair_update_service_service_id_foreign` (`service_id`),
CONSTRAINT `repair_update_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `motorbike_repair_services_lists` (`id`) ON DELETE CASCADE,
CONSTRAINT `repair_update_service_update_id_foreign` FOREIGN KEY (`update_id`) REFERENCES `motorbike_repair_updates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_update_service');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
