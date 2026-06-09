<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `discountables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `condition` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_use` int unsigned NOT NULL DEFAULT '0',
  `discountable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `discountable_id` bigint unsigned NOT NULL,
  `discount_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `discountables_discountable_type_discountable_id_index` (`discountable_type`,`discountable_id`),
  KEY `discountables_discount_id_index` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('discountables');
    }
};
