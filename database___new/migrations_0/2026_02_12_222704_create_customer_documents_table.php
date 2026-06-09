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
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_deleted')->nullable()->default('0');
            $table->unsignedBigInteger('customer_id')->index('customer_documents_customer_id_foreign');
            $table->unsignedBigInteger('document_type_id')->index('customer_documents_document_type_id_foreign');
            $table->string('file_name');
            $table->string('file_path');
            $table->boolean('sent_private')->default(false);
            $table->string('file_format', 10);
            $table->string('document_number', 100)->default('');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('booking_id')->nullable()->index('customer_documents_booking_id_foreign');
            $table->unsignedBigInteger('motorbike_id')->nullable()->index('customer_documents_motorbike_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
    }
};
