<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates renting_booking_items only if missing (e.g. migration was marked run but table was dropped).
     */
    public function up(): void
    {
        if (Schema::hasTable('renting_booking_items')) {
            return;
        }

        Schema::create('renting_booking_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('motorbike_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('weekly_rent', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('renting_bookings')->onDelete('restrict');
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renting_booking_items');
    }
};
