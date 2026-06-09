<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mot_bookings', 'booking_slot_end')) {
            Schema::table('mot_bookings', function (Blueprint $table) {
                $table->dateTime('booking_slot_end')->nullable()->default(now()->addMinutes(30));
            });
        }
    }

    public function down(): void
    {
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->dropColumn('booking_slot_end');
        });
    }
};
