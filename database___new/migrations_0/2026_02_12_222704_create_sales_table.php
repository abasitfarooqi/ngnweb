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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('sales_user_id_foreign');
            $table->unsignedBigInteger('product_id')->index('sales_product_id_foreign');
            $table->string('brand_name', 125)->nullable();
            $table->string('generic_name', 125)->nullable();
            $table->string('category', 125)->nullable();
            $table->double('orginal_price')->nullable();
            $table->double('sell_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->double('profit')->nullable();
            $table->double('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
