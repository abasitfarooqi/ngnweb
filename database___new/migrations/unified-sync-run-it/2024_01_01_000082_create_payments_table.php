<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('payments'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `motorcycle_id` bigint(20) DEFAULT NULL,
  `registration` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `rental_deposit` decimal(8,2) DEFAULT NULL,
  `rental_price` decimal(8,2) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `received` decimal(8,2) DEFAULT NULL,
  `outstanding` decimal(8,2) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `payment_due_date` datetime DEFAULT NULL,
  `payment_due_count` bigint(20) DEFAULT NULL,
  `payment_next_date` datetime DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `paid` varchar(70) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `auth_user` varchar(255) DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT '',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
