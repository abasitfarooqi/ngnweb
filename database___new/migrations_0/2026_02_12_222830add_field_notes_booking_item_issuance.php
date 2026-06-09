<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('booking_issuance_items', 'notes')) {
            return;
        }
        Schema::table('booking_issuance_items', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('booking_issuance_items', 'notes')) {
            return;
        }
        Schema::table('booking_issuance_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
