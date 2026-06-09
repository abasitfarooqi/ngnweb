<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('backup_club_member_purchases'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `backup_club_member_purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `club_member_id` bigint(20) unsigned DEFAULT NULL,
  `percent` decimal(8,4) DEFAULT NULL,
  `total` decimal(8,4) DEFAULT NULL,
  `discount` decimal(8,4) DEFAULT NULL,
  `is_redeemed` tinyint(1) NOT NULL DEFAULT 0,
  `redeem_amount` decimal(8,4) DEFAULT NULL,
  `pos_invoice` varchar(255) DEFAULT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `original_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_club_member_purchases');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
