<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Business Model for Shipping Methods:
     *
     * 1. Basic Information
     * - Each shipping method has a unique name and optional slug for URL-friendly identification
     * - Logo and link_url fields allow for visual representation and external links
     * - Description provides details about the shipping service
     *
     * 2. Pricing
     * - shipping_amount stores the base cost for this shipping method
     * - Zero cost possible for special cases like in-store pickup
     *
     * 3. Availability Control
     * - is_enabled flag controls whether this shipping method is currently available
     * - in_store_pickup differentiates between delivery and pickup methods
     *
     * Example Use Cases:
     * 1. Standard Delivery
     * - name: "Standard Delivery"
     * - shipping_amount: 4.99
     * - in_store_pickup: false
     *
     * 2. Express Delivery
     * - name: "Next Day Delivery"
     * - shipping_amount: 9.99
     * - in_store_pickup: false
     *
     * 3. Store Pickup
     * - name: "Click & Collect"
     * - shipping_amount: 0.00
     * - in_store_pickup: true
     */
    public function up(): void
    {
        Schema::create('ec_shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the shipping method');
            $table->string('slug')->nullable();
            $table->string('logo')->default('-');
            $table->string('link_url')->default('-');
            $table->string('description')->default('-');
            $table->decimal('shipping_amount', 10, 2)->default(0)->comment('Shipping cost for the order, it could be 0 if choose to self pick up.');
            $table->boolean('is_enabled')->default(false);
            $table->boolean('in_store_pickup')->default(false)->comment('True. If the shipping method is in store pickup');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_shipping_methods');
    }
};
