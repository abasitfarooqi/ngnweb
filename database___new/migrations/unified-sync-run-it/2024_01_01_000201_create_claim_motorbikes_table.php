<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('claim_motorbikes'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `claim_motorbikes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `notes` text NOT NULL,
  `case_date` datetime NOT NULL,
  `is_received` tinyint(1) NOT NULL DEFAULT 0,
  `received_date` datetime DEFAULT NULL,
  `is_returned` tinyint(1) NOT NULL DEFAULT 0,
  `returned_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_motorbikes_motorbike_id_foreign` (`motorbike_id`),
  KEY `claim_motorbikes_branch_id_foreign` (`branch_id`),
  KEY `claim_motorbikes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_motorbikes');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
