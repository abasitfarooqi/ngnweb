<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments_paypal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status')->default('pending');
            $table->string('payer_email')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_id')->nullable();
            $table->decimal('paypal_fee', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->json('payment_response')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments_paypal');
    }
};
