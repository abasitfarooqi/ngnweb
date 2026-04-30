<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('renting_other_charges_transactions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_other_charges_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL,
  `charges_id` bigint(20) unsigned NOT NULL,
  `transaction_type_id` bigint(20) unsigned NOT NULL,
  `payment_method_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `notes` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_other_charges_transactions_charges_id_foreign` (`charges_id`),
  KEY `renting_other_charges_transactions_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_other_charges_transactions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
