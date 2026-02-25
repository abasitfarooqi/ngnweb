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
        if (! Schema::hasTable('order_refunds')) {
            Schema::create('order_refunds', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('refund_reason')->nullable();
                $table->string('refund_amount')->nullable();
                $table->string('status');
                $table->string('notes');
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};
