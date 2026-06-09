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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('order_refunds'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_refunds` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`refund_reason` longtext COLLATE utf8mb4_unicode_ci,
`refund_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`status` enum('pending','treatment','partial-refund','refunded','cancelled','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
`notes` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
`order_id` bigint unsigned NOT NULL,
`user_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `order_refunds_order_id_index` (`order_id`),
KEY `order_refunds_user_id_index` (`user_id`),
CONSTRAINT `order_refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
CONSTRAINT `order_refunds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users-old` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
