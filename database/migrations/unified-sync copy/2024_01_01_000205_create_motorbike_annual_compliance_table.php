<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_annual_compliance'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_annual_compliance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `year` year(4) NOT NULL,
  `mot_status` varchar(100) NOT NULL,
  `road_tax_status` varchar(100) NOT NULL,
  `insurance_status` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax_due_date` date DEFAULT NULL,
  `insurance_due_date` date DEFAULT NULL,
  `mot_due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_annual_compliance_motorbike_id_foreign` (`motorbike_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2359 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_annual_compliance');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
