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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('judopay_recurring_holds'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_recurring_holds` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`scope_type` enum('customer','subscription') COLLATE utf8mb4_unicode_ci NOT NULL,
`scope_id` bigint unsigned NOT NULL,
`is_active` tinyint(1) NOT NULL DEFAULT '1',
`created_by` bigint unsigned DEFAULT NULL,
`updated_by` bigint unsigned DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `judopay_recurring_holds_scope_unique` (`scope_type`,`scope_id`),
KEY `judopay_recurring_holds_created_by_foreign` (`created_by`),
KEY `judopay_recurring_holds_updated_by_foreign` (`updated_by`),
KEY `judopay_recurring_holds_scope_active_idx` (`scope_type`,`is_active`),
CONSTRAINT `judopay_recurring_holds_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
CONSTRAINT `judopay_recurring_holds_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_recurring_holds');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
