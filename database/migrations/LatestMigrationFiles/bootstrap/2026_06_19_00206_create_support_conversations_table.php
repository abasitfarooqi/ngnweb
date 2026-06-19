<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('support_conversations')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `support_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_auth_id` bigint unsigned DEFAULT NULL,
  `service_booking_id` bigint unsigned DEFAULT NULL,
  `assigned_backpack_user_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `last_message_at` timestamp NULL DEFAULT NULL,
  `first_customer_message_at` timestamp NULL DEFAULT NULL,
  `external_ai_session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_conversations_uuid_unique` (`uuid`),
  KEY `support_conversations_customer_auth_id_status_index` (`customer_auth_id`,`status`),
  KEY `support_conversations_service_booking_id_status_index` (`service_booking_id`,`status`),
  KEY `support_conversations_assigned_backpack_user_id_status_index` (`assigned_backpack_user_id`,`status`),
  KEY `support_conversations_last_message_at_index` (`last_message_at`),
  CONSTRAINT `support_conversations_assigned_backpack_user_id_foreign` FOREIGN KEY (`assigned_backpack_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_conversations_customer_auth_id_foreign` FOREIGN KEY (`customer_auth_id`) REFERENCES `customer_auths` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_conversations_service_booking_id_foreign` FOREIGN KEY (`service_booking_id`) REFERENCES `service_bookings` (`id`) ON DELETE SET NULL
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
