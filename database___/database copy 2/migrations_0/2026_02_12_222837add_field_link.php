<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('mot_bookings', 'payment_link')) {
            return;
        }
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->string('payment_link')->nullable()->after('title');
            $table->boolean('is_paid')->default(false)->nullable(false);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('mot_bookings', 'payment_link')) {
            return;
        }
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->dropColumn('payment_link');
            $table->boolean('is_paid')->nullable()->change();
        });
    }
};
