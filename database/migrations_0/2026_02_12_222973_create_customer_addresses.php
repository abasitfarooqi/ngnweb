<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hypothetical Customer Address Events:
     *
     * 1. Customer Registration (2024-03-20)
     * - Customer creates account and adds first address
     * - Address marked as default for both billing and shipping
     * - Fields: name, address, phone, etc populated
     *
     * 2. Additional Address Added (2024-03-25)
     * - Customer adds office address
     * - Not set as default
     * - Company name field utilized
     *
     * 3. Address Update (2024-04-01)
     * - Customer moves, updates street address
     * - Phone number changed
     * - Maintains same address ID
     *
     * Field Reasoning:
     * - last_name/first_name: Required for shipping labels and billing
     * - company_name: Optional for business deliveries
     * - street_address: Primary address line
     * - street_address_plus: For apt numbers, suite numbers, etc
     * - postcode: Required for shipping calculations and validation
     * - city: Required for address completeness
     * - phone_number: Optional but needed for delivery contact
     * - is_default: One address must be marked default per type
     * - type: Supports multiple address use cases
     * - country_id: Links to system countries for validation
     * - customer_id: Associates address with customer account
     */
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('last_name')->default('');
            $table->string('first_name')->default('');
            $table->string('company_name')->nullable();
            $table->string('street_address')->default('');
            $table->string('street_address_plus')->nullable();
            $table->string('postcode')->default('');
            $table->string('city')->default('');
            $table->string('phone_number')->nullable();
            $table->boolean('is_default')->default(false);
            $table->enum('type', ['billing', 'shipping', 'office', 'other']);
            $table->foreignId('country_id')->constrained('system_countries')->onDelete('restrict');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
