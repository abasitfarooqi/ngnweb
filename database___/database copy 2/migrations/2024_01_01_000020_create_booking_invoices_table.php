<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_date` date DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_invoices_booking_id_foreign` (`booking_id`),
  KEY `booking_invoices_user_id_foreign` (`user_id`),
  CONSTRAINT `booking_invoices_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `booking_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_invoices');
    }
};
