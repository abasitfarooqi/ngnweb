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
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pr_id')->index('purchase_request_items_pr_id_foreign');
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
            $table->unsignedBigInteger('created_by')->nullable()->index('purchase_request_items_created_by_foreign');
            $table->timestamps();
            $table->unsignedBigInteger('brand_name_id')->index('purchase_request_items_brand_name_id_foreign');
            $table->unsignedBigInteger('bike_model_id')->index('purchase_request_items_bike_model_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};
