<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_closing` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `notice_details` text COLLATE utf8mb4_unicode_ci,
  `notice_checked` tinyint(1) NOT NULL DEFAULT '0',
  `collect_details` text COLLATE utf8mb4_unicode_ci,
  `collect_date` date DEFAULT NULL,
  `collect_time` time DEFAULT NULL,
  `collect_checked` tinyint(1) NOT NULL DEFAULT '0',
  `damages_checked` tinyint(1) NOT NULL DEFAULT '0',
  `pcn_checked` tinyint(1) NOT NULL DEFAULT '0',
  `pending_checked` tinyint(1) NOT NULL DEFAULT '0',
  `deposit_checked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_closing_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_closing');
    }
};
