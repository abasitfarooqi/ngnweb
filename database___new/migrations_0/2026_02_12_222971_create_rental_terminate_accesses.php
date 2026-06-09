<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_terminate_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('booking_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('booking_id')->references('id')->on('renting_bookings');
            $table->string('passcode')->default('');
            $table->datetime('expire_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_terminate_accesses');
    }
};
