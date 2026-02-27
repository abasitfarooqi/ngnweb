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
        Schema::create('renting_service_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string(column: 'video_path'); // path of uploaded video
            $table->timestamp('recorded_at')->nullable(); // date of service video
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('renting_bookings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renting_service_videos');
    }
};
