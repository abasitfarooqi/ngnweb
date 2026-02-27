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
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->foreign(['booking_id'])->references(['id'])->on('renting_bookings')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->dropForeign('booking_invoices_booking_id_foreign');
            $table->dropForeign('booking_invoices_user_id_foreign');
        });
    }
};
