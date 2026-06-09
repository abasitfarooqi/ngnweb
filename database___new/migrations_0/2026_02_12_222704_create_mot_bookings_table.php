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
        Schema::create('mot_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id')->index('mot_bookings_branch_id_foreign');
            $table->string('title')->nullable()->default('MOT Booking');
            $table->string('payment_link')->nullable();
            $table->dateTime('date_of_appointment')->default('2024-06-13 11:57:29');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->string('vehicle_registration');
            $table->string('vehicle_chassis')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->boolean('all_day')->nullable()->default(false);
            $table->string('customer_name');
            $table->string('customer_contact');
            $table->string('customer_email');
            $table->enum('status', ['pending', 'available', 'completed', 'cancelled', 'booked'])->nullable()->default('available');
            $table->text('notes')->nullable();
            $table->string('background_color')->default('white');
            $table->string('text_color')->default('black');
            $table->timestamps();
            $table->boolean('is_paid')->default(false);
            $table->string('payment_method')->nullable();
            $table->string('payment_notes')->nullable();
            $table->boolean('is_validate')->default(true);
            $table->unsignedBigInteger('user_id')->nullable();

            $table->unique(['start', 'end'], 'start_end_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mot_bookings');
    }
};
