<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('renting_transactions')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL DEFAULT '2000-01-01',
  `booking_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `transaction_type_id` bigint unsigned NOT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint unsigned NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_transactions_booking_id_foreign` (`booking_id`),
  KEY `renting_transactions_invoice_id_foreign` (`invoice_id`),
  KEY `renting_transactions_transaction_type_id_foreign` (`transaction_type_id`),
  KEY `renting_transactions_payment_method_id_foreign` (`payment_method_id`),
  KEY `renting_transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `renting_transactions_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`),
  CONSTRAINT `renting_transactions_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `booking_invoices` (`id`),
  CONSTRAINT `renting_transactions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `renting_transactions_transaction_type_id_foreign` FOREIGN KEY (`transaction_type_id`) REFERENCES `transaction_types` (`id`),
  CONSTRAINT `renting_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
