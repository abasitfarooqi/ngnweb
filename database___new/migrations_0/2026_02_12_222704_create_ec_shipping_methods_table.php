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
        Schema::create('ec_shipping_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Name of the shipping method');
            $table->string('slug')->nullable();
            $table->string('logo')->default('-');
            $table->string('link_url')->default('-');
            $table->string('description')->default('-');
            $table->decimal('shipping_amount', 10)->default(0)->comment('Shipping cost for the order, it could be 0 if choose to self pick up.');
            $table->boolean('is_enabled')->default(false);
            $table->boolean('in_store_pickup')->default(false)->comment('True. If the shipping method is in store pickup');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_shipping_methods');
    }
};
