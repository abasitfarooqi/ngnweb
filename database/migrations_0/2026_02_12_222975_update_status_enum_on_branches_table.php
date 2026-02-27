<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only run on MySQL - SQLite doesn't support MODIFY COLUMN
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE mot_bookings MODIFY COLUMN status ENUM('pending', 'available', 'completed', 'cancelled', 'booked') DEFAULT 'available'");
        }
    }

    public function down(): void
    {
        // Only run on MySQL - SQLite doesn't support MODIFY COLUMN
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE mot_bookings MODIFY COLUMN status ENUM('pending', 'available', 'completed', 'cancelled') DEFAULT 'available'");
        }
    }
};
