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
        Schema::create('ec_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('order_date')->useCurrent()->comment('Date order was processed');
            $table->string('order_status')->default('pending')->comment('Order status, pending, processing, shipped, completed, cancelled, etc.');
            $table->decimal('total_amount', 10)->default(0)->comment('Total amount before shipping, tax and discounts');
            $table->decimal('discount', 10)->default(0)->comment('Discount amount applied to order, ');
            $table->decimal('tax', 10)->default(0)->comment('Tax amount for the order');
            $table->decimal('grand_total', 10)->default(0)->comment('Final total after shipping, tax and discounts');
            $table->decimal('shipping_cost', 10)->default(0)->comment('Shipping cost for the order, it could be 0 if choose to self pick up.');
            $table->string('shipping_status')->default('pending')->comment('Shipping status, pending, processing, shipped, completed, cancelled, etc.');
            $table->dateTime('shipping_date')->nullable()->comment('Date shipping was processed');
            $table->string('payment_status')->default('pending')->comment('Current payment status, pending, paid, failed, refunded, etc.');
            $table->string('currency')->default('GBP')->comment('Currency of the order');
            $table->dateTime('payment_date')->nullable()->comment('Date payment was processed');
            $table->string('payment_reference')->nullable()->comment('Reference number/ID received from payment gateway after successful transaction. paypal, stripe, zettle, etc.');
            $table->unsignedBigInteger('customer_id')->index('ec_orders_customer_id_foreign');
            $table->unsignedBigInteger('shipping_method_id')->index('ec_orders_shipping_method_id_foreign');
            $table->unsignedBigInteger('payment_method_id')->index('ec_orders_payment_method_id_foreign');
            $table->unsignedBigInteger('customer_address_id')->index('ec_orders_customer_address_id_foreign');
            $table->timestamps();
            $table->unsignedBigInteger('branch_id')->nullable()->index('ec_orders_branch_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_orders');
    }
};
