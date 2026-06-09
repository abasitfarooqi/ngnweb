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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('booking_issuance_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_issuance_items` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`booking_item_id` bigint unsigned NOT NULL,
`issued_by_user_id` bigint unsigned NOT NULL,
`current_mileage` int NOT NULL,
`is_video_recorded` tinyint(1) NOT NULL DEFAULT '0',
`accessories_checked` tinyint(1) NOT NULL DEFAULT '0',
`issuance_branch` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`notes` text COLLATE utf8mb4_unicode_ci,
`is_insured` tinyint(1) DEFAULT '0',
PRIMARY KEY (`id`),
KEY `booking_issuance_items_issued_by_user_id_foreign` (`issued_by_user_id`),
KEY `booking_issuance_items_booking_item_id_index` (`booking_item_id`),
CONSTRAINT `booking_issuance_items_booking_item_id_foreign` FOREIGN KEY (`booking_item_id`) REFERENCES `renting_booking_items` (`id`),
CONSTRAINT `booking_issuance_items_issued_by_user_id_foreign` FOREIGN KEY (`issued_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=696 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_issuance_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
