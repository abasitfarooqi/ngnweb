<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('rentals'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `rentals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `make` varchar(70) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `model` varchar(70) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `engine` varchar(70) NOT NULL,
  `year` varchar(70) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `colour` varchar(70) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `signature` blob DEFAULT NULL,
  `motorcycle_id` bigint(20) DEFAULT NULL,
  `registration` varchar(255) DEFAULT NULL,
  `deposit` decimal(8,2) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `auth_user` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
