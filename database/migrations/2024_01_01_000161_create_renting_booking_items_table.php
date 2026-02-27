<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_booking_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_booking_items_booking_id_foreign` (`booking_id`),
  KEY `renting_booking_items_motorbike_id_foreign` (`motorbike_id`),
  KEY `renting_booking_items_user_id_foreign` (`user_id`),
  CONSTRAINT `renting_booking_items_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `renting_booking_items_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `renting_booking_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_booking_items');
    }
};
