<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mot_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable(false);
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->string('title')->nullable()->default('MOT Booking');
            $table->dateTime('date_of_appointment')->default(now());
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->string('vehicle_registration');
            $table->string('vehicle_chassis')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->boolean('all_day')->nullable(true)->default(false);
            $table->string('customer_name');
            $table->string('customer_contact');
            $table->string('customer_email');
            $table->enum('status', ['pending', 'available', 'completed', 'cancelled'])->default('available');
            $table->text('notes')->nullable(true);
            $table->string('background_color')->default('white');
            $table->string('text_color')->default('black');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mot_bookings');
    }
};
