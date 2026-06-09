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
        Schema::create('attribute_value_product_attributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('attribute_value_id')->nullable()->index('attribute_value_product_attributes_attribute_value_id_foreign');
            $table->unsignedBigInteger('product_attribute_id')->index('attribute_value_product_attributes_product_attribute_id_foreign');
            $table->string('product_custom_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_product_attributes');
    }
};
