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
        Schema::create('payments_paypal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('payments_paypal_customer_id_foreign');
            $table->string('order_id');
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 10);
            $table->string('currency', 3);
            $table->string('status')->default('pending');
            $table->string('payer_email')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_id')->nullable();
            $table->decimal('paypal_fee', 10)->nullable();
            $table->decimal('net_amount', 10)->nullable();
            $table->json('payment_response')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_paypal');
    }
};
