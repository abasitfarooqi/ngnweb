<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_member_spendings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '2025-12-18 17:09:53',
  `club_member_id` bigint unsigned NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `club_member_spendings_pos_invoice_unique` (`pos_invoice`),
  KEY `club_member_spendings_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_spendings_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('club_member_spendings');
    }
};
