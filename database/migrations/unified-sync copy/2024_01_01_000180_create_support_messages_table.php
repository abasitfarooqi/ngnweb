<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: connected target-only table
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('support_messages'));
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
  KEY `support_messages_sender_type_created_at_index` (`sender_type`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
