<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('recovered_motorbikes'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `recovered_motorbikes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `case_date` datetime NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `returned_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recovered_motorbikes_user_id_foreign` (`user_id`),
  KEY `recovered_motorbikes_branch_id_foreign` (`branch_id`),
  KEY `recovered_motorbikes_motorbike_id_foreign` (`motorbike_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('recovered_motorbikes');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
