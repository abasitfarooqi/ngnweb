<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE mot_bookings MODIFY COLUMN status ENUM('pending', 'available', 'completed', 'cancelled', 'booked') DEFAULT 'available'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE mot_bookings MODIFY COLUMN status ENUM('pending', 'available', 'completed', 'cancelled') DEFAULT 'available'");
    }
};
