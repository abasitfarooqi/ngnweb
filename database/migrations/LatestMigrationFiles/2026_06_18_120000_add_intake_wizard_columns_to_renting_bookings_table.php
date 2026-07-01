<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Wizard resume fields for same-day rental intake (Flux Admin new-booking). */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('renting_bookings')) {
            return;
        }

        if (! Schema::hasColumn('renting_bookings', 'intake_step')) {
            Schema::table('renting_bookings', function (Blueprint $table): void {
                $table->unsignedTinyInteger('intake_step')->nullable()->after('notes');
            });
        }

        if (! Schema::hasColumn('renting_bookings', 'intake_meta')) {
            Schema::table('renting_bookings', function (Blueprint $table): void {
                $table->json('intake_meta')->nullable()->after('intake_step');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('renting_bookings')) {
            return;
        }

        if (Schema::hasColumn('renting_bookings', 'intake_meta')) {
            Schema::table('renting_bookings', function (Blueprint $table): void {
                $table->dropColumn('intake_meta');
            });
        }

        if (Schema::hasColumn('renting_bookings', 'intake_step')) {
            Schema::table('renting_bookings', function (Blueprint $table): void {
                $table->dropColumn('intake_step');
            });
        }
    }
};
