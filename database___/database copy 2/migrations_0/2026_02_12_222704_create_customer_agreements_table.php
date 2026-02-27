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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index('customer_agreements_booking_id_foreign');
            $table->unsignedBigInteger('customer_id')->index('customer_agreements_customer_id_foreign');
            $table->unsignedBigInteger('document_type_id')->index('customer_agreements_document_type_id_foreign');
            $table->string('file_name');
            $table->string('file_path');
            $table->boolean('sent_private')->default(false);
            $table->string('file_format', 10);
            $table->string('document_number', 100)->default('');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
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
