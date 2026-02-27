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
        Schema::create('order_shippings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->date('shipped_at');
            $table->date('received_at');
            $table->date('returned_at');
            $table->string('tracking_number')->nullable();
            $table->string('tracking_number_url')->nullable();
            $table->json('voucher')->nullable();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('carrier_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shippings');
    }
};
