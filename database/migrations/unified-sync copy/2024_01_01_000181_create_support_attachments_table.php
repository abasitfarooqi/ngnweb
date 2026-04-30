<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: connected target-only table
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('support_attachments'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `support_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint unsigned NOT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL DEFAULT '0',
  `uploaded_by_customer_auth_id` bigint unsigned DEFAULT NULL,
  `uploaded_by_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_attachments_uploaded_by_customer_auth_id_foreign` (`uploaded_by_customer_auth_id`),
  KEY `support_attachments_uploaded_by_user_id_foreign` (`uploaded_by_user_id`),
  KEY `support_attachments_message_id_created_at_index` (`message_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('support_attachments');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
