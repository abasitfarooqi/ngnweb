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
        Schema::create('purchase_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date')->default('2024-04-09 17:14:20');
            $table->string('note')->default('-');
            $table->unsignedBigInteger('created_by')->nullable()->index('purchase_request_created_by_foreign');
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request');
    }
};
