<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ec_order_shippings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('ec_orders')->onDelete('restrict');

            // Fulfillment Method
            $table->enum('fulfillment_method', ['carrier', 'pickup'])->default('carrier')->comment('Type of fulfillment: carrier or in-store pickup');

            // Unified status for both shipping and pickup
            $table->string('status')->default('processing')->comment('
                Shipping Flow:
                    - processing -> ready_for_carrier -> shipped -> delivered
                Pickup Flow:
                    - processing -> ready_for_pickup -> picked_up -> collected
                Return Flow:
                    - initiated -> in_transit -> received
            ');

            // Business timestamps
            $table->datetime('processing_at')->nullable()->comment('When we start processing');
            $table->datetime('ready_at')->nullable()->comment('When ready for carrier/pickup');
            $table->datetime('shipped_at')->nullable()->comment('When shipped/picked up');
            $table->datetime('completed_at')->nullable()->comment('When delivered/collected');

            // Return handling
            $table->enum('return_method', ['carrier', 'in_store', 'others'])->nullable()->comment('Method of return: carrier or in-store');
            $table->datetime('return_initiated_at')->nullable()->comment('When return was initiated');
            $table->datetime('return_shipped_at')->nullable()->comment('When return was shipped');
            $table->datetime('return_received_at')->nullable()->comment('When return was received');

            // Carrier information (minimal)
            $table->string('carrier')->nullable()->comment('Carrier service used (Royal Mail, DHL, etc)');
            $table->string('tracking_number')->nullable()->comment('Shipping tracking number');
            $table->string('tracking_url')->nullable()->comment('URL to track shipment');

            // Additional information
            $table->text('notes')->nullable()->comment('Additional notes or instructions for shipping/pickup');
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'fulfillment_method']);
            $table->index('tracking_number');
        });

        /*
            Status Definitions:

            Shipping Flow:
                - processing: Order is being prepared for shipping
                - ready_for_carrier: Order is ready to be handed over to the carrier
                - shipped: Order has been shipped
                - delivered: Order has been delivered to the customer

            Pickup Flow:
                - processing: Order is being prepared for pickup
                - ready_for_pickup: Order is ready at the store for pickup
                - picked_up: Customer has picked up the order
                - collected: Order has been collected and the transaction is complete

            Return Flow:
                - initiated: Customer has initiated a return
                - in_transit: Returned order is in transit back to the store/carrier
                - received: Returned order has been received and processed
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_order_shippings');
    }
};
