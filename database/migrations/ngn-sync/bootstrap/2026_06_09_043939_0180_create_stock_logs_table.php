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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('stock_logs'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `stock_logs` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`picture` text COLLATE utf8mb4_unicode_ci,
`qty` int NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`branch_id` bigint unsigned NOT NULL DEFAULT '1',
`user_id` bigint unsigned DEFAULT NULL,
`sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `stock_logs_branch_id_foreign` (`branch_id`),
KEY `stock_logs_user_id_foreign` (`user_id`),
CONSTRAINT `stock_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
CONSTRAINT `stock_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=693 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
