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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('pcn_tol_requests'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_tol_requests` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`pcn_case_id` bigint unsigned DEFAULT NULL,
`update_id` bigint unsigned NOT NULL,
`request_date` date NOT NULL DEFAULT '2025-08-21',
`status` enum('pending','sent','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
`full_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`letter_sent_at` timestamp NULL DEFAULT NULL,
`note` text COLLATE utf8mb4_unicode_ci,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`user_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `pcn_tol_requests_update_id_foreign` (`update_id`),
KEY `pcn_tol_requests_user_id_foreign` (`user_id`),
KEY `pcn_tol_requests_pcn_case_id_foreign` (`pcn_case_id`),
CONSTRAINT `pcn_tol_requests_pcn_case_id_foreign` FOREIGN KEY (`pcn_case_id`) REFERENCES `pcn_cases` (`id`),
CONSTRAINT `pcn_tol_requests_update_id_foreign` FOREIGN KEY (`update_id`) REFERENCES `pcn_case_updates` (`id`),
CONSTRAINT `pcn_tol_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('pcn_tol_requests');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
