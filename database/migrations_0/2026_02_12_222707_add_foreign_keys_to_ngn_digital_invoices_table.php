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
        Schema::table('ngn_digital_invoices', function (Blueprint $table) {
            $table->foreign(['booking_invoice_id'])->references(['id'])->on('booking_invoices')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_digital_invoices', function (Blueprint $table) {
            $table->dropForeign('ngn_digital_invoices_booking_invoice_id_foreign');
            $table->dropForeign('ngn_digital_invoices_created_by_foreign');
        });
    }
};
