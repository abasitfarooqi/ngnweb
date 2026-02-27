<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('mot_bookings') && ! Schema::hasColumn('mot_bookings', 'background_color')) {
            Schema::table('mot_bookings', function (Blueprint $table) {
                $table->string('background_color')->nullable();
            });
        }

        if (Schema::hasTable('mot_bookings') && ! Schema::hasColumn('mot_bookings', 'text_color')) {
            Schema::table('mot_bookings', function (Blueprint $table) {
                $table->string('text_color')->nullable();
            });
        }

        if (Schema::hasTable('mot_bookings') && ! Schema::hasColumn('mot_bookings', 'all_day')) {
            Schema::table('mot_bookings', function (Blueprint $table) {
                $table->boolean('all_day')->nullable(true)->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->dropColumn('background_color');
            $table->dropColumn('text_color');
            $table->dropColumn('all_day');
        });
    }
};
