<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Business Model for Orders:
         *
         * 1. Basic Information
         * - Each order has a unique ID and timestamp
         * - Customer ID links to customer account
         * - Shipping method ID links to shipping method
         * - Payment method ID links to payment method
         * - Customer address ID links to customer address
         *
         * 2. Order Status
         * - Pending: Order has been placed but not yet processed
         * - Processing: Order is being prepared for shipping
         * - Shipped: Order has been shipped
         * - Completed: Order has been delivered to the customer
         * - Cancelled: Order has been cancelled
         *
         * 3. Payment Status
         * - Pending: Payment has not been processed
         * - Paid: Payment has been successfully processed
         * - Failed: Payment failed or was not successful
         * - Refunded: Payment has been refunded
         *
         * 4. Currency
         * - Default currency is GBP
         *
         * 5. Payment Reference
         * - Reference number/ID received from payment gateway after successful transaction. paypal, stripe, zettle, etc.
         *
         * 6. Foreign Keys
         * - Customer ID links to customer account
         * - Shipping method ID links to shipping method
         * - Payment method ID links to payment method
         * - Customer address ID links to customer address
         */
        Schema::create('ec_orders', function (Blueprint $table) {
            $table->id()->from(5000);

            $table->timestamp('order_date')->useCurrent()->comment('Date order was processed');
            $table->string('order_status')->default('pending')->comment('Order status, pending, processing, shipped, completed, cancelled, etc.');

            $table->decimal('total_amount', 10, 2)->default(0)->comment('Total amount before shipping, tax and discounts');
            $table->decimal('discount', 10, 2)->default(0)->comment('Discount amount applied to order, ');
            $table->decimal('tax', 10, 2)->default(0)->comment('Tax amount for the order');
            $table->decimal('grand_total', 10, 2)->default(0)->comment('Final total after shipping, tax and discounts');

            $table->decimal('shipping_cost', 10, 2)->default(0)->comment('Shipping cost for the order, it could be 0 if choose to self pick up.');
            $table->string('shipping_status')->default('pending')->comment('Shipping status, pending, processing, shipped, completed, cancelled, etc.');
            $table->datetime('shipping_date')->nullable()->comment('Date shipping was processed');

            $table->string('payment_method')->comment('Selected payment method, singular payment method for the order.');
            $table->string('payment_status')->default('pending')->comment('Current payment status, pending, paid, failed, refunded, etc.');
            $table->string('currency')->default('GBP')->comment('Currency of the order');

            $table->datetime('payment_date')->nullable()->comment('Date payment was processed');
            $table->string('payment_reference')->nullable()->comment('Reference number/ID received from payment gateway after successful transaction. paypal, stripe, zettle, etc.');

            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict')->comment('Foreign key to customers table');
            $table->foreignId('shipping_method_id')->constrained('ec_shipping_methods')->onDelete('restrict')->comment('Foreign key to shipping methods table');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict')->comment('Foreign key to payment methods table');
            $table->foreignId('customer_address_id')->constrained('customer_addresses')->onDelete('restrict')->comment('Foreign key to customer addresses table');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_orders');
    }
};
