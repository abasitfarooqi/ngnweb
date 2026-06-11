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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('service_bookings'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `service_bookings` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`customer_id` bigint unsigned DEFAULT NULL,
`customer_auth_id` bigint unsigned DEFAULT NULL,
`conversation_id` bigint unsigned DEFAULT NULL,
`submission_context` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`enquiry_type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`service_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`description` text COLLATE utf8mb4_unicode_ci,
`requires_schedule` tinyint(1) NOT NULL DEFAULT '0',
`booking_date` date DEFAULT NULL,
`booking_time` time DEFAULT NULL,
`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
`is_dealt` tinyint(1) NOT NULL DEFAULT '0',
`dealt_by_user_id` bigint unsigned DEFAULT NULL,
`notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`reg_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `service_bookings_customer_id_index` (`customer_id`),
KEY `service_bookings_customer_auth_id_index` (`customer_auth_id`),
KEY `service_bookings_enquiry_type_index` (`enquiry_type`),
KEY `service_bookings_submission_context_index` (`submission_context`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
