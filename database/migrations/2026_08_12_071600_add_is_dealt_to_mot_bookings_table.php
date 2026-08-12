<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('mot_bookings', 'is_dealt')) {
            return;
        }

        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->boolean('is_dealt')->default(false)->after('is_paid');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('mot_bookings', 'is_dealt')) {
            return;
        }

        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->dropColumn('is_dealt');
        });
    }
};
