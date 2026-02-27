<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Business Model for Order Items:
         *
         * 1. Basic Information
         * - Each order item has a unique ID and timestamp
         * - Order ID links to the parent order
         * - Product ID links to the product
         * - Product name and SKU provide details about the product
         * - Quantity and unit price store the quantity and price of the product at the time of the order
         * - Total price and line total store the total price and line total after discounts and taxes
         *
         * 2. Discounts and Taxes
         * - Discount and tax fields store the discount and tax amounts applied to the product
         * - Line total is calculated as unit price times quantity, minus discounts, plus taxes
         *
         * 3. Foreign Keys
         * - Order ID links to the parent order
         * - Product ID links to the product
         *
         * 4. Indexes
         */
        if (!Schema::hasTable('ec_order_items')) {
            Schema::create('ec_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('ec_orders')->onDelete('restrict')->comment('Foreign key to orders table');
            $table->unsignedBigInteger('product_id')->nullable()->comment('Foreign key to products table (removed - products table was deleted)');
            $table->string('product_name')->comment('Name of the product at the time of the order');
            $table->string('sku')->comment('SKU of the product at the time of the order');
            $table->integer('quantity')->default(1)->comment('Quantity of the product in the order');
            $table->decimal('unit_price', 10, 2)->default(0)->comment('Unit price of the product at the time of the order');
            $table->decimal('total_price', 10, 2)->default(0)->comment('Total price of the product at the time of the order');
            $table->decimal('discount', 10, 2)->default(0)->comment('Discount amount applied to the product at the time of the order');
            $table->decimal('tax', 10, 2)->default(0)->comment('Tax amount applied to the product at the time of the order');
            $table->decimal('line_total', 10, 2)->default(0)->comment('Final total after shipping, tax and discounts');
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_order_items');
    }
};
