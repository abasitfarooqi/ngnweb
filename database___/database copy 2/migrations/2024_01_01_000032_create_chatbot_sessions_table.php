<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `chatbot_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bot_response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `admin_id` bigint unsigned DEFAULT NULL,
  `admin_replied_at` timestamp NULL DEFAULT NULL,
  `bot_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `is_typing` tinyint(1) NOT NULL DEFAULT '0',
  `message_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `message_order` int NOT NULL DEFAULT '1',
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_sessions_session_id_message_order_index` (`session_id`,`message_order`),
  KEY `chatbot_sessions_created_at_index` (`created_at`),
  KEY `chatbot_sessions_user_id_index` (`user_id`),
  KEY `chatbot_sessions_user_email_index` (`user_email`),
  KEY `chatbot_sessions_session_id_index` (`session_id`),
  KEY `chatbot_sessions_session_id_message_status_index` (`session_id`,`message_status`),
  KEY `chatbot_sessions_admin_id_index` (`admin_id`),
  KEY `chatbot_sessions_session_id_bot_disabled_index` (`session_id`,`bot_disabled`),
  CONSTRAINT `chatbot_sessions_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_sessions');
    }
};
