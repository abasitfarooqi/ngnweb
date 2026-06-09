<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Align production columns to ngn_clean canonical schema. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('service_bookings')) {
            return;
        }

        if (! Schema::hasColumn('service_bookings', 'customer_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `customer_id` bigint unsigned NULL DEFAULT NULL AFTER `id`');
        }

        if (! Schema::hasColumn('service_bookings', 'customer_auth_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `customer_auth_id` bigint unsigned NULL DEFAULT NULL AFTER `customer_id`');
        }

        if (! Schema::hasColumn('service_bookings', 'submission_context')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `submission_context` varchar(40) NULL DEFAULT NULL AFTER `customer_auth_id`');
        }

        if (! Schema::hasColumn('service_bookings', 'enquiry_type')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `enquiry_type` varchar(80) NULL DEFAULT NULL AFTER `submission_context`');
        }

        if (! Schema::hasColumn('service_bookings', 'subject')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `subject` varchar(255) NULL DEFAULT NULL AFTER `service_type`');
        }

    }

    public function down(): void
    {
        // Manual rollback if required.
    }
};
