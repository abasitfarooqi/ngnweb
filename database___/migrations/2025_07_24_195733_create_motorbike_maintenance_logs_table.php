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
        Schema::create('motorbike_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('motorbike_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('cost', 10, 2)->default(0);
            $table->dateTime('serviced_at');
            $table->string('description');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('motorbike_id')->references('id')->on('motorbikes');
            $table->foreign('booking_id')->references('id')->on('renting_bookings');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_maintenance_logs');
    }
};
