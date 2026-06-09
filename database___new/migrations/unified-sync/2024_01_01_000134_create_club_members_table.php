<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('club_members'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Last user who updated this record',
  `make` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `year` varchar(4) DEFAULT NULL,
  `vrm` varchar(12) DEFAULT NULL,
  `dob_code` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `tc_agreed` tinyint(1) NOT NULL DEFAULT 1,
  `passkey` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `ngn_partner_id` bigint(20) unsigned DEFAULT NULL,
  `is_partner` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `club_members_ngn_partner_id_foreign` (`ngn_partner_id`),
  KEY `club_members_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2671 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('club_members');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
