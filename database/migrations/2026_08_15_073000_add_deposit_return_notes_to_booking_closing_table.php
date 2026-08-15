<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_closing', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_closing', 'deposit_return_notes')) {
                $table->text('deposit_return_notes')->nullable()->after('deposit_checked');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_closing', function (Blueprint $table) {
            if (Schema::hasColumn('booking_closing', 'deposit_return_notes')) {
                $table->dropColumn('deposit_return_notes');
            }
        });
    }
};
