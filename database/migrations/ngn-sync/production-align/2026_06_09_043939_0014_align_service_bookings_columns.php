<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('service_bookings')) {
            return;
        }

        if (! Schema::hasColumn('service_bookings', 'customer_auth_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `customer_auth_id` bigint unsigned DEFAULT NULL AFTER `customer_id`');
        }
        if (! Schema::hasColumn('service_bookings', 'customer_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `customer_id` bigint unsigned DEFAULT NULL AFTER `id`');
        }
        if (! Schema::hasColumn('service_bookings', 'conversation_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `conversation_id` bigint unsigned DEFAULT NULL AFTER `customer_auth_id`');
        }
        if (! Schema::hasColumn('service_bookings', 'enquiry_type')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `enquiry_type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `submission_context`');
        }
        if (! Schema::hasColumn('service_bookings', 'is_dealt')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `is_dealt` tinyint(1) NOT NULL DEFAULT 0 AFTER `status`');
        }
        if (! Schema::hasColumn('service_bookings', 'dealt_by_user_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `dealt_by_user_id` bigint unsigned DEFAULT NULL AFTER `is_dealt`');
        }
        if (! Schema::hasColumn('service_bookings', 'notes')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `dealt_by_user_id`');
        }
        if (! Schema::hasColumn('service_bookings', 'subject')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `service_type`');
        }
        if (! Schema::hasColumn('service_bookings', 'submission_context')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `submission_context` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `customer_auth_id`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
