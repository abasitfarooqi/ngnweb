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

        if (! Schema::hasColumn('service_bookings', 'conversation_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `conversation_id` bigint unsigned DEFAULT NULL');
        }

        if (! Schema::hasColumn('service_bookings', 'is_dealt')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `is_dealt` tinyint(1) NOT NULL DEFAULT 0');
        }

        if (! Schema::hasColumn('service_bookings', 'dealt_by_user_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `dealt_by_user_id` bigint unsigned DEFAULT NULL');
        }

        if (! Schema::hasColumn('service_bookings', 'notes')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `notes` text DEFAULT NULL');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
