<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `sms_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(34) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_sid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_sent` timestamp NULL DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  `direction` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `messaging_service_sid` varchar(34) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_media` int NOT NULL DEFAULT '0',
  `num_segments` int NOT NULL DEFAULT '1',
  `price` decimal(8,4) DEFAULT NULL,
  `price_unit` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subresource_uris` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `sms_messages_chk_1` CHECK (json_valid(`subresource_uris`))
) ENGINE=InnoDB AUTO_INCREMENT=11346 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
