<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('user_sessions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `user_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint(20) unsigned NOT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  `logout_time` timestamp NULL DEFAULT NULL,
  `session_duration` int(11) DEFAULT NULL,
  `pages_visited` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pages_visited`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_sessions_club_member_id_foreign` (`club_member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1539 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
