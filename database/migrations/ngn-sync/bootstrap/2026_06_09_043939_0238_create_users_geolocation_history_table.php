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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('users_geolocation_history'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `users_geolocation_history` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`deleted_at` timestamp NULL DEFAULT NULL,
`ip_api` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`extreme_ip_lookup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`user_id` bigint unsigned NOT NULL,
`order_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `users_geolocation_history_user_id_index` (`user_id`),
KEY `users_geolocation_history_order_id_index` (`order_id`),
CONSTRAINT `users_geolocation_history_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
CONSTRAINT `users_geolocation_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users-old` (`id`) ON DELETE CASCADE,
CONSTRAINT `users_geolocation_history_chk_1` CHECK (json_valid(`ip_api`)),
CONSTRAINT `users_geolocation_history_chk_2` CHECK (json_valid(`extreme_ip_lookup`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('users_geolocation_history');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
