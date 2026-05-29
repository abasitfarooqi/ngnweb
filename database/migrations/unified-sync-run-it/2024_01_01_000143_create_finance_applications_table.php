<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('finance_applications'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `finance_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `sold_by` varchar(255) DEFAULT NULL COMMENT 'Person who sold the bike; set once, do not modify',
  `is_posted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deposit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `contract_date` datetime DEFAULT NULL,
  `first_instalment_date` date DEFAULT NULL,
  `weekly_instalment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_monthly` tinyint(1) NOT NULL DEFAULT 0,
  `motorbike_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_items` text DEFAULT NULL,
  `extra` decimal(10,2) DEFAULT NULL,
  `log_book_sent` tinyint(1) NOT NULL DEFAULT 0,
  `is_cancelled` tinyint(1) NOT NULL DEFAULT 0,
  `reason_of_cancellation` varchar(255) DEFAULT NULL,
  `logbook_transfer_date` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `is_used_extended` tinyint(1) NOT NULL DEFAULT 0,
  `is_used_extended_custom` tinyint(1) NOT NULL DEFAULT 0,
  `is_new_latest` tinyint(1) NOT NULL DEFAULT 0,
  `is_used_latest` tinyint(1) NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) NOT NULL DEFAULT 0,
  `subscription_option` varchar(10) DEFAULT NULL,
  `subs_payment_date` tinyint(3) unsigned DEFAULT NULL COMMENT 'Day of month (1-31) customer pays for 12-month subscription',
  PRIMARY KEY (`id`),
  KEY `finance_applications_customer_id_foreign` (`customer_id`),
  KEY `finance_applications_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_applications');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
