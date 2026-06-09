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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('backup_club_member_purchases'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `backup_club_member_purchases` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`date` date DEFAULT NULL,
`club_member_id` bigint unsigned DEFAULT NULL,
`percent` decimal(8,4) DEFAULT NULL,
`total` decimal(8,4) DEFAULT NULL,
`discount` decimal(8,4) DEFAULT NULL,
`is_redeemed` tinyint(1) NOT NULL DEFAULT '0',
`redeem_amount` decimal(8,4) DEFAULT NULL,
`pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`user_id` bigint unsigned DEFAULT NULL,
`original_id` bigint unsigned NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
