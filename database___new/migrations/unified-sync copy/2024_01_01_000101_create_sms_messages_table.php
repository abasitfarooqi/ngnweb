<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('sms_messages'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `sms_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(34) DEFAULT NULL,
  `account_sid` varchar(255) DEFAULT NULL,
  `api_version` varchar(10) NOT NULL,
  `body` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_sent` timestamp NULL DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  `direction` varchar(20) NOT NULL,
  `error_code` varchar(10) DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `from` varchar(15) NOT NULL,
  `to` varchar(15) NOT NULL,
  `messaging_service_sid` varchar(34) DEFAULT NULL,
  `num_media` int(11) NOT NULL DEFAULT 0,
  `num_segments` int(11) NOT NULL DEFAULT 1,
  `price` decimal(8,4) DEFAULT NULL,
  `price_unit` varchar(3) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `subresource_uris` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subresource_uris`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12668 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
