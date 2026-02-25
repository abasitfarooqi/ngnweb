<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renting_other_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable(false);
            $table->foreign('booking_id')->references('id')->on('renting_bookings')->onDelete('restrict');
            $table->string('description')->nullable(false);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_other_charges');
    }
};
