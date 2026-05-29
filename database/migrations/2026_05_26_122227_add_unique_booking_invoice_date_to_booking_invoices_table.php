<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->unique(['booking_id', 'invoice_date'], 'unique_booking_invoice_date');
        });
    }

    public function down(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->dropUnique('unique_booking_invoice_date');
        });
    }
};
