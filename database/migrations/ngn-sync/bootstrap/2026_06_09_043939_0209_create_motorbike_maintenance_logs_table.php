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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_maintenance_logs'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_maintenance_logs` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`motorbike_id` bigint unsigned NOT NULL,
`booking_id` bigint unsigned DEFAULT NULL,
`user_id` bigint unsigned NOT NULL,
`cost` decimal(10,2) NOT NULL DEFAULT '0.00',
`serviced_at` datetime NOT NULL,
`description` varchar(1500) COLLATE utf8mb4_unicode_ci NOT NULL,
`note` text COLLATE utf8mb4_unicode_ci,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `motorbike_maintenance_logs_motorbike_id_foreign` (`motorbike_id`),
KEY `motorbike_maintenance_logs_booking_id_foreign` (`booking_id`),
KEY `motorbike_maintenance_logs_user_id_foreign` (`user_id`),
CONSTRAINT `motorbike_maintenance_logs_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`),
CONSTRAINT `motorbike_maintenance_logs_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`),
CONSTRAINT `motorbike_maintenance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_maintenance_logs');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
