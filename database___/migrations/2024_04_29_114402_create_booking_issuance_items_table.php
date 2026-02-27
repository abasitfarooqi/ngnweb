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
        Schema::create('booking_issuance_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_item_id');
            $table->unsignedBigInteger('issued_by_user_id');
            $table->integer('current_mileage');
            $table->boolean('is_video_recorded')->default(false);
            $table->boolean('accessories_checked')->default(false);
            $table->string('issuance_branch', 20)->nullable(false);

            $table->timestamps();

            $table->foreign('booking_item_id')->references('id')->on('renting_booking_items')
                ->onDelete('restrict');
            $table->foreign('issued_by_user_id')->references('id')->on('users')
                ->onDelete('restrict');

            $table->index('booking_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_issuance_items');
    }
};
