<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('mot_bookings', 'is_validate')) {
            return;
        }
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->boolean('is_validate')->default(true);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('mot_bookings', 'is_validate')) {
            return;
        }
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->dropColumn('is_validate');
        });
    }
};
