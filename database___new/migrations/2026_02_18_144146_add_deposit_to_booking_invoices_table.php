<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_invoices', 'deposit')) {
                $table->decimal('deposit', 10, 2)->default(0)->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('booking_invoices', 'deposit')) {
                $table->dropColumn('deposit');
            }
        });
    }
};