<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `mot_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'MOT Booking',
  `payment_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_appointment` datetime NOT NULL DEFAULT '2024-06-13 11:57:29',
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `vehicle_registration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_chassis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `all_day` tinyint(1) DEFAULT '0',
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','available','completed','cancelled','booked') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `background_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'white',
  `text_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'black',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_validate` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `start_end_unique` (`start`,`end`),
  KEY `mot_bookings_branch_id_foreign` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=703 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('mot_bookings');
    }
};
