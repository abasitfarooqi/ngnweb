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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('vehicle_issuances'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_issuances` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`customer_id` bigint unsigned DEFAULT NULL,
`issue_date` datetime NOT NULL,
`user_id` bigint unsigned NOT NULL,
`branch_id` bigint unsigned NOT NULL,
`motorbike_id` bigint unsigned NOT NULL,
`notes` text COLLATE utf8mb4_unicode_ci,
`is_returned` tinyint(1) NOT NULL DEFAULT '0',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `vehicle_issuances_user_id_foreign` (`user_id`),
KEY `vehicle_issuances_branch_id_foreign` (`branch_id`),
KEY `vehicle_issuances_motorbike_id_foreign` (`motorbike_id`),
KEY `vehicle_issuances_customer_id_foreign` (`customer_id`),
CONSTRAINT `vehicle_issuances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
CONSTRAINT `vehicle_issuances_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `vehicle_issuances_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`),
CONSTRAINT `vehicle_issuances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_issuances');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
