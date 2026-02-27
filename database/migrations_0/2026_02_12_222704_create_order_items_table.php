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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name')->nullable()->comment('The product name at the moment of buying');
            $table->string('sku')->nullable()->index();
            $table->string('product_type');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->integer('unit_price_amount');
            $table->unsignedBigInteger('order_id')->nullable()->index();

            $table->index(['product_type', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
