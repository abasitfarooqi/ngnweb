<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_bookings', 'is_dealt')) {
                $table->boolean('is_dealt')->default(false)->after('status');
            }
            if (! Schema::hasColumn('service_bookings', 'dealt_by_user_id')) {
                $table->unsignedBigInteger('dealt_by_user_id')->nullable()->after('is_dealt');
            }
            if (! Schema::hasColumn('service_bookings', 'notes')) {
                $table->text('notes')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table): void {
            if (Schema::hasColumn('service_bookings', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('service_bookings', 'dealt_by_user_id')) {
                $table->dropColumn('dealt_by_user_id');
            }
            if (Schema::hasColumn('service_bookings', 'is_dealt')) {
                $table->dropColumn('is_dealt');
            }
        });
    }
};
