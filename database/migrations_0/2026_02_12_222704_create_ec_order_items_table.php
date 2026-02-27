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
        Schema::create('ec_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('ec_order_items_order_id_foreign');
            $table->unsignedBigInteger('product_id')->index('ec_order_items_product_id_foreign');
            $table->string('product_name')->comment('Name of the product at the time of the order');
            $table->string('sku')->comment('SKU of the product at the time of the order');
            $table->integer('quantity')->default(1)->comment('Quantity of the product in the order');
            $table->decimal('unit_price', 10)->default(0)->comment('Unit price of the product at the time of the order');
            $table->decimal('total_price', 10)->default(0)->comment('Total price of the product at the time of the order');
            $table->decimal('discount', 10)->default(0)->comment('Discount amount applied to the product at the time of the order');
            $table->decimal('tax', 10)->default(0)->comment('Tax amount applied to the product at the time of the order');
            $table->decimal('line_total', 10)->default(0)->comment('Final total after shipping, tax and discounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_order_items');
    }
};
