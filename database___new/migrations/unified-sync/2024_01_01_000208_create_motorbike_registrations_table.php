<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_registrations'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_registrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `registration_number` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `motorbike_registrations_motorbike_id_registration_number_unique` (`motorbike_id`,`registration_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2349 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_registrations');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
