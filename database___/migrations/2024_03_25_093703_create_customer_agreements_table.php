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
        Schema::create('customer_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('document_type_id');
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('file_format', 10);
            $table->string('document_number', 100)->default('');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->foreign('booking_id')->references('id')->on('renting_bookings')->onDelete('restrict');
            $table->foreign('customer_id')->references('id')->on('customers')
                ->onDelete('restrict');
            $table->foreign('document_type_id')->references('id')->on('document_types')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_agreements');
    }
};
