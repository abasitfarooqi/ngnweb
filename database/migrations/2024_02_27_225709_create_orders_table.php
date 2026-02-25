<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('number');
                $table->integer('price_amount')->nullable();
                $table->string('status');
                $table->string('currency');
                $table->integer('shipping_total')->nullable();
                $table->string('shipping_method')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('parent_order_id')->nullable();
                $table->unsignedBigInteger('payment_method_id')->nullable();
                $table->unsignedBigInteger('shipping_address_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('parent_order_id')->references('id')->on('orders')->onDelete('set null');
                $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
                $table->foreign('shipping_address_id')->references('id')->on('user_addresses')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
