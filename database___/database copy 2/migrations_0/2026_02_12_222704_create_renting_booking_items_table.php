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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index('renting_booking_items_booking_id_foreign');
            $table->unsignedBigInteger('motorbike_id')->index('renting_booking_items_motorbike_id_foreign');
            $table->unsignedBigInteger('user_id')->index('renting_booking_items_user_id_foreign');
            $table->decimal('weekly_rent', 10)->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_posted')->default(false);
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
