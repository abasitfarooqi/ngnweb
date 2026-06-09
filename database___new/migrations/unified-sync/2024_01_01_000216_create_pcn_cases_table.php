<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('pcn_cases'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pcn_number` varchar(255) NOT NULL,
  `date_of_contravention` date NOT NULL,
  `date_of_letter_issued` date DEFAULT NULL,
  `time_of_contravention` time NOT NULL,
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `isClosed` tinyint(1) NOT NULL DEFAULT 0,
  `full_amount` decimal(10,2) NOT NULL,
  `reduced_amount` decimal(10,2) DEFAULT NULL,
  `picture_url` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `council_link` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_police` tinyint(1) NOT NULL DEFAULT 0,
  `is_whatsapp_sent` tinyint(1) DEFAULT 0,
  `whatsapp_last_reminder_sent_at` datetime DEFAULT NULL,
  `is_sms_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sms_last_sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pcn_cases_motorbike_id_foreign` (`motorbike_id`),
  KEY `pcn_cases_customer_id_foreign` (`customer_id`),
  KEY `pcn_cases_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('pcn_cases');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
