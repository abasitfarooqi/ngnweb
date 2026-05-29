<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbikes_repair'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbikes_repair` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `arrival_date` datetime NOT NULL,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `notes` text NOT NULL,
  `is_repaired` tinyint(1) NOT NULL DEFAULT 0,
  `repaired_date` datetime DEFAULT NULL,
  `is_returned` tinyint(1) NOT NULL DEFAULT 0,
  `returned_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbikes_repair_motorbike_id_foreign` (`motorbike_id`),
  KEY `motorbikes_repair_branch_id_foreign` (`branch_id`),
  KEY `motorbikes_repair_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_repair');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
