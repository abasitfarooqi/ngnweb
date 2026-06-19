<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('support_messages')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `support_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `sender_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `sender_customer_auth_id` bigint unsigned DEFAULT NULL,
  `sender_user_id` bigint unsigned DEFAULT NULL,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta` json DEFAULT NULL,
  `read_at_customer` timestamp NULL DEFAULT NULL,
  `read_at_staff` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_messages_sender_customer_auth_id_foreign` (`sender_customer_auth_id`),
  KEY `support_messages_sender_user_id_foreign` (`sender_user_id`),
  KEY `support_messages_conversation_id_created_at_index` (`conversation_id`,`created_at`),
  KEY `support_messages_sender_type_created_at_index` (`sender_type`,`created_at`),
  CONSTRAINT `support_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `support_conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_messages_sender_customer_auth_id_foreign` FOREIGN KEY (`sender_customer_auth_id`) REFERENCES `customer_auths` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_messages_sender_user_id_foreign` FOREIGN KEY (`sender_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
