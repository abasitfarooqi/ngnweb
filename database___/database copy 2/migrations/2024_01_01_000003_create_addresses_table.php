<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `street_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `street_address_plus` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `post_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `city` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `phone_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('billing','shipping') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
