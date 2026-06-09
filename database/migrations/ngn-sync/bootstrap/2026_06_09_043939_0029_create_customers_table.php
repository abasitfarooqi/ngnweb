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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('customers'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `customers` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`dob` date DEFAULT NULL,
`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`is_register` tinyint(1) NOT NULL DEFAULT '0',
`is_club` tinyint(1) NOT NULL DEFAULT '0',
`phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'London',
`country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UK',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`reputation_note` text COLLATE utf8mb4_unicode_ci,
`emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Name of the emergency contact person',
`whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Whatsapp number',
`Customer Full Name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`last name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`PHONE1` int DEFAULT NULL,
`creatd` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`updated` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`WHATSAPP NO.` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`rating` int DEFAULT NULL,
`license_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`license_expiry_date` date DEFAULT NULL,
`license_issuance_authority` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`license_issuance_date` date DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `customers_email_unique` (`email`),
KEY `customers_is_club_index` (`is_club`)
) ENGINE=InnoDB AUTO_INCREMENT=445 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
