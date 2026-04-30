<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('customers'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `is_register` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL DEFAULT 'London',
  `country` varchar(255) NOT NULL DEFAULT 'UK',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `reputation_note` text,
  `emergency_contact` varchar(255) DEFAULT NULL COMMENT 'Name of the emergency contact person',
  `whatsapp` varchar(255) DEFAULT NULL COMMENT 'Whatsapp number',
  `Customer Full Name` varchar(50) DEFAULT NULL,
  `last name` varchar(50) DEFAULT NULL,
  `PHONE1` int(11) DEFAULT NULL,
  `creatd` varchar(50) DEFAULT NULL,
  `updated` varchar(50) DEFAULT NULL,
  `WHATSAPP NO.` varchar(50) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  `license_issuance_authority` varchar(255) DEFAULT NULL,
  `license_issuance_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=445 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
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
