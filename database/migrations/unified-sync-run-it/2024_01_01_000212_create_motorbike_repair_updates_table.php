<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_repair_updates'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_repair_updates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_repair_id` bigint(20) unsigned NOT NULL,
  `job_description` text NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_repair_updates_motorbike_repair_id_foreign` (`motorbike_repair_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_repair_updates');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
