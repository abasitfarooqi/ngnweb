<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_member_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '2024-09-30 14:56:56',
  `club_member_id` bigint unsigned NOT NULL,
  `percent` decimal(5,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `is_redeemed` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redeem_amount` decimal(8,4) DEFAULT NULL,
  `price` decimal(8,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `club_member_purchases_pos_invoice_unique` (`pos_invoice`),
  KEY `club_member_purchases_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_purchases_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14494 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('club_member_purchases');
    }
};
