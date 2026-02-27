<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_annual_compliance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `year` year NOT NULL,
  `mot_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `road_tax_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `insurance_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax_due_date` date DEFAULT NULL,
  `insurance_due_date` date DEFAULT NULL,
  `mot_due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_annual_compliance_motorbike_id_foreign` (`motorbike_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_annual_compliance');
    }
};
