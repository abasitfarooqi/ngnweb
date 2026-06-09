<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('customer_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable(); // Make nullable
            $table->foreign('booking_id')->references('id')->on('renting_bookings')->onDelete('restrict');

            $table->unsignedBigInteger('motorbike_id')->nullable(); // Make nullable
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_documents', function (Blueprint $table) {
            //
        });
    }
};
