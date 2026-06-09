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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('new_motorbikes'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `new_motorbikes` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`purchase_date` date NOT NULL DEFAULT '2024-09-25',
`VRM` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`make` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`colour` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`engine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`VIM` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`branch_id` bigint unsigned NOT NULL,
`user_id` bigint unsigned NOT NULL,
`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
`is_vrm` tinyint(1) NOT NULL DEFAULT '0',
`is_migrated` tinyint(1) NOT NULL DEFAULT '0',
`migrated_at` date DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `new_motorbikes_branch_id_foreign` (`branch_id`),
KEY `new_motorbikes_user_id_foreign` (`user_id`),
CONSTRAINT `new_motorbikes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
CONSTRAINT `new_motorbikes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('new_motorbikes');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
