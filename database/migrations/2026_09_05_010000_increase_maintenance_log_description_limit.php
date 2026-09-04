<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('motorbike_maintenance_logs')
            && Schema::hasColumn('motorbike_maintenance_logs', 'description')) {
            DB::statement(
                "ALTER TABLE `motorbike_maintenance_logs` MODIFY `description` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL"
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('motorbike_maintenance_logs')
            && Schema::hasColumn('motorbike_maintenance_logs', 'description')) {
            DB::statement(
                "ALTER TABLE `motorbike_maintenance_logs` MODIFY `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL"
            );
        }
    }
};
