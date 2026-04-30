<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('users_geolocation_history'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `users_geolocation_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `ip_api` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `extreme_ip_lookup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_geolocation_history_user_id_index` (`user_id`),
  KEY `users_geolocation_history_order_id_index` (`order_id`),
  CONSTRAINT `users_geolocation_history_chk_1` CHECK (json_valid(`ip_api`)),
  CONSTRAINT `users_geolocation_history_chk_2` CHECK (json_valid(`extreme_ip_lookup`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
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
