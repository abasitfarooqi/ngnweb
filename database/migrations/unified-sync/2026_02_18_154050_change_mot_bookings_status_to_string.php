<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Replace ENUM with VARCHAR to accept production values without truncation.
        DB::statement("ALTER TABLE `mot_bookings` MODIFY `status` VARCHAR(50) NULL DEFAULT 'available'");
    }

    public function down(): void
    {
        // Revert to the original enum (WARNING: will fail if existing rows contain values not in the enum).
        DB::statement("ALTER TABLE `mot_bookings` MODIFY `status` ENUM('pending','available','completed','cancelled','booked') NULL DEFAULT 'available'");
    }
};
