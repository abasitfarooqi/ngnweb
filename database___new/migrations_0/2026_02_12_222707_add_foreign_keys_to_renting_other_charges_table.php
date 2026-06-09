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
        Schema::table('renting_other_charges', function (Blueprint $table) {
            $table->foreign(['booking_id'])->references(['id'])->on('renting_bookings')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('renting_other_charges', function (Blueprint $table) {
            $table->dropForeign('renting_other_charges_booking_id_foreign');
        });
    }
};
