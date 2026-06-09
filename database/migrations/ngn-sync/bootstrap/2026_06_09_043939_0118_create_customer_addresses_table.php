<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('customer_addresses'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_addresses` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`street_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`street_address_plus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`is_default` tinyint(1) NOT NULL DEFAULT '0',
`type` enum('billing','shipping','office','other') COLLATE utf8mb4_unicode_ci NOT NULL,
`country_id` bigint unsigned NOT NULL,
`customer_id` bigint unsigned NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `customer_addresses_country_id_foreign` (`country_id`),
KEY `customer_addresses_customer_id_foreign` (`customer_id`),
CONSTRAINT `customer_addresses_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `system_countries` (`id`),
CONSTRAINT `customer_addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
