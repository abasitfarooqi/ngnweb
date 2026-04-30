<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('customer_addresses'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `company_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) NOT NULL DEFAULT '',
  `street_address_plus` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `phone_number` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `type` enum('billing','shipping','office','other') NOT NULL,
  `country_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_country_id_foreign` (`country_id`),
  KEY `customer_addresses_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
