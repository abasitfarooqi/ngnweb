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
        Schema::create('booking_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable(false);
            $table->foreign('booking_id')->references('id')->on('renting_bookings')->onDelete('restrict');
            $table->date('invoice_date')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->date('paid_date')->nullable();
            $table->string('state')->default('DRAFT');
            $table->text('notes')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_invoices');
    }
};
