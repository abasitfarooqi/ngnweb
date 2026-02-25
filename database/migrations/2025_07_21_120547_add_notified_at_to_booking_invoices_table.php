<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->dateTime('notified_at')->nullable()->default(null)->after('is_paid');
        });
    }

    public function down(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->dropColumn('notified_at');
        });
    }
};
