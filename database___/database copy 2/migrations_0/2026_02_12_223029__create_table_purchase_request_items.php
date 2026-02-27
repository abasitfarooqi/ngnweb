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
        if (! Schema::hasTable('purchase_request_items')) {
            Schema::create('purchase_request_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pr_id');
                $table->foreign('pr_id')->references('id')->on('purchase_requests');
                $table->unsignedBigInteger('brand_id');
                $table->foreign('brand_id')->references('id')->on('brands');
                $table->string('model');
                $table->string('color');
                $table->string('year');
                $table->string('chassis_no');
                $table->string('reg_no');
                $table->string('part_number');
                $table->string('part_position');
                $table->string('link_one')->nullable();
                $table->string('link_two')->nullable();
                $table->integer('quantity');
                $table->string('image')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};
