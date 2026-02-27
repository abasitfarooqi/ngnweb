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
        Schema::create('ec_order_shippings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('ec_order_shippings_order_id_foreign');
            $table->enum('fulfillment_method', ['carrier', 'pickup'])->default('carrier')->comment('Type of fulfillment: carrier or in-store pickup');
            $table->string('status')->default('processing')->comment('
                Shipping Flow:
                    - processing -> ready_for_carrier -> shipped -> delivered
                Pickup Flow:
                    - processing -> ready_for_pickup -> picked_up -> collected
                Return Flow:
                    - initiated -> in_transit -> received
            ');
            $table->dateTime('processing_at')->nullable()->comment('When we start processing');
            $table->dateTime('ready_at')->nullable()->comment('When ready for carrier/pickup');
            $table->dateTime('shipped_at')->nullable()->comment('When shipped/picked up');
            $table->dateTime('completed_at')->nullable()->comment('When delivered/collected');
            $table->enum('return_method', ['carrier', 'in_store', 'others'])->nullable()->comment('Method of return: carrier or in-store');
            $table->dateTime('return_initiated_at')->nullable()->comment('When return was initiated');
            $table->dateTime('return_shipped_at')->nullable()->comment('When return was shipped');
            $table->dateTime('return_received_at')->nullable()->comment('When return was received');
            $table->string('carrier')->nullable()->comment('Carrier service used (Royal Mail, DHL, etc)');
            $table->string('tracking_number')->nullable()->index()->comment('Shipping tracking number');
            $table->string('tracking_url')->nullable()->comment('URL to track shipment');
            $table->text('notes')->nullable()->comment('Additional notes or instructions for shipping/pickup');
            $table->timestamps();

            $table->index(['status', 'fulfillment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_order_shippings');
    }
};
