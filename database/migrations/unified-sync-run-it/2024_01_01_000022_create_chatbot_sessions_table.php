<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('chatbot_sessions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `chatbot_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) NOT NULL,
  `user_message` text NOT NULL,
  `bot_response` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `admin_replied_at` timestamp NULL DEFAULT NULL,
  `bot_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_typing` tinyint(1) NOT NULL DEFAULT 0,
  `message_status` varchar(255) NOT NULL DEFAULT 'sent',
  `message_order` int(11) NOT NULL DEFAULT 1,
  `user_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
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
  KEY `chatbot_sessions_session_id_bot_disabled_index` (`session_id`,`bot_disabled`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_sessions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
