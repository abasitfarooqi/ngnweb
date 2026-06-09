<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_invoices', 'notified_at')) {
                $table->timestamp('notified_at')->nullable()->after('is_paid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('booking_invoices', 'notified_at')) {
                $table->dropColumn('notified_at');
            }
        });
    }
};
