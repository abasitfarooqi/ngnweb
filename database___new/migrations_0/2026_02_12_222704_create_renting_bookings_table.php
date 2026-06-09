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
        Schema::create('renting_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('renting_bookings_customer_id_foreign');
            $table->unsignedBigInteger('user_id')->index('renting_bookings_user_id_foreign');
            $table->dateTime('start_date')->default('2024-11-26 16:24:03');
            $table->date('due_date')->nullable();
            $table->string('state')->default('DRAFT');
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
            $table->decimal('deposit', 10)->default(0);
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renting_bookings');
    }
};
