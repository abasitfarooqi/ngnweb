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
        if (! Schema::hasTable('order_shippings')) {
            Schema::create('order_shippings', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->dateTime('shipped_at');
                $table->dateTime('received_at');
                $table->dateTime('returned_at');
                $table->string('tracking_number')->nullable();
                $table->string('tracking_number_url')->nullable();
                $table->string('voucher')->nullable();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('carrier_id')->nullable();
                $table->foreign('carrier_id')->references('id')->on('carriers');
                $table->foreign('order_id')->references('id')->on('orders');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shippings');
    }
};
