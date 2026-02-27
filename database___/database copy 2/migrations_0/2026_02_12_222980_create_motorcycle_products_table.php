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
        Schema::create('oxford_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('description')->nullable();
            $table->string('ean')->nullable();
            $table->decimal('rrp_less_vat', 8, 2)->default(0);
            $table->decimal('rrp_inc_vat', 8, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('catford_stock')->nullable()->default(0);
            $table->string('estimated_delivery')->nullable();
            $table->string('image_file_name')->nullable();
            $table->boolean('vatable')->nullable();
            $table->boolean('obsolete')->nullable();
            $table->string('category')->nullable();
            $table->string('supplier')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('cost_price')->nullable();
            $table->string('brand')->nullable();
            $table->text('extended_description')->nullable();
            $table->string('variation')->nullable();
            $table->timestamp('date_added')->nullable();
            $table->string('super_product_name')->nullable();
            $table->string('colour')->nullable();
            $table->text('image_url')->nullable();
            $table->string('model')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oxford_products');
    }
};
