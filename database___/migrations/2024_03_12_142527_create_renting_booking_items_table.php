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
        Schema::create('renting_booking_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable(false);
            $table->foreign('booking_id')->references('id')->on('renting_bookings')->onDelete('restrict');
            $table->unsignedBigInteger('motorbike_id')->nullable(false);
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable(); // next due date
            $table->date('end_date')->nullable(true); // upon return, contract is closed, terminated, and end_date is set.
            $table->boolean('is_posted')->default(false); // // that checked require true of booking posted true or booking posted only true after that true
            $table->timestamps();
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
