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
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('service_type');
            $table->text('description')->nullable();
            $table->boolean('requires_schedule')->default(false);
            $table->date('booking_date')->nullable();
            $table->time('booking_time')->nullable();
            $table->string('status')->default('Pending');
            $table->string('fullname');
            $table->string('phone');
            $table->string('reg_no');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
