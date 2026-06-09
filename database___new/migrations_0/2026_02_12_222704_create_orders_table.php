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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('number', 32);
            $table->integer('price_amount')->nullable();
            $table->string('status', 32);
            $table->string('currency');
            $table->integer('shipping_total')->nullable();
            $table->string('shipping_method')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('parent_order_id')->nullable()->index();
            $table->unsignedBigInteger('payment_method_id')->nullable()->index();
            $table->unsignedBigInteger('shipping_address_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
