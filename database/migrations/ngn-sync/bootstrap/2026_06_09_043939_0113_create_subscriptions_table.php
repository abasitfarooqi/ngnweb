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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('subscriptions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `subscriptions` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned NOT NULL,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`stripe_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`stripe_plan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`quantity` int DEFAULT NULL,
`trial_ends_at` timestamp NULL DEFAULT NULL,
`ends_at` timestamp NULL DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
