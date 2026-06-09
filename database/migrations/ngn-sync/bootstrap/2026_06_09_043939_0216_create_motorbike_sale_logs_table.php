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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_sale_logs'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_sale_logs` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`motorbike_id` bigint unsigned NOT NULL,
`motorbikes_sale_id` bigint unsigned NOT NULL,
`user_id` bigint unsigned NOT NULL,
`username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`reg_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`is_sold` tinyint(1) NOT NULL,
`buyer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`buyer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`buyer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`buyer_address` text COLLATE utf8mb4_unicode_ci,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `motorbike_sale_logs_motorbike_id_foreign` (`motorbike_id`),
KEY `motorbike_sale_logs_motorbikes_sale_id_foreign` (`motorbikes_sale_id`),
CONSTRAINT `motorbike_sale_logs_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`) ON DELETE CASCADE,
CONSTRAINT `motorbike_sale_logs_motorbikes_sale_id_foreign` FOREIGN KEY (`motorbikes_sale_id`) REFERENCES `motorbikes_sale` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_sale_logs');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
