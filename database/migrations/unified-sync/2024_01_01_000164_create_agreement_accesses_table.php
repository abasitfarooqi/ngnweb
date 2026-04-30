<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('agreement_accesses'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `agreement_accesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `passcode` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `booking_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agreement_accesses_customer_id_foreign` (`customer_id`),
  KEY `agreement_accesses_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('agreement_accesses');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
