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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('club_members'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_members` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`user_id` bigint unsigned DEFAULT NULL COMMENT 'Last user who updated this record',
`customer_id` bigint unsigned DEFAULT NULL,
`make` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`model` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`year` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`vrm` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`dob_code` date DEFAULT NULL,
`is_active` tinyint(1) NOT NULL DEFAULT '1',
`tc_agreed` tinyint(1) NOT NULL DEFAULT '1',
`passkey` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`email_sent` tinyint(1) NOT NULL DEFAULT '0',
`ngn_partner_id` bigint unsigned DEFAULT NULL,
`is_partner` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
KEY `club_members_ngn_partner_id_foreign` (`ngn_partner_id`),
KEY `club_members_user_id_foreign` (`user_id`),
KEY `club_members_customer_id_index` (`customer_id`),
CONSTRAINT `club_members_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
CONSTRAINT `club_members_ngn_partner_id_foreign` FOREIGN KEY (`ngn_partner_id`) REFERENCES `ngn_partners` (`id`),
CONSTRAINT `club_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2672 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('club_members');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
