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
        if (!Schema::hasTable('ngn_products')) {
            Schema::create('ngn_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('image_url')->nullable();
            $table->string('super_product_name');
            $table->text('description')->nullable();
            $table->text('extended_description')->nullable();
            $table->string('variation')->nullable();
            $table->decimal('cost_price', 8, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->integer('catford_stock')->default(0);
            $table->string('model')->nullable();
            $table->string('colour')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('ean')->nullable();
            $table->decimal('rrp_less_vat', 8, 2)->default(0.00);
            $table->decimal('rrp_inc_vat', 8, 2)->default(0.00);
            $table->date('estimated_delivery')->nullable();
            $table->boolean('vatable')->default(true);
            $table->boolean('obsolete')->default(false);
            $table->boolean('dead')->default(false);
            $table->string('image_file_name')->nullable();
            $table->string('product_type')->nullable()->default(''); // Added field with max length

            // Foreign key relationships
            $table->unsignedBigInteger('model_id')->nullable()->default(1);
            $table->unsignedBigInteger('category_id')->nullable()->default(1);
            $table->unsignedBigInteger('brand_id')->nullable()->default(1);
            $table->unsignedBigInteger('product_type_id')->nullable()->default(1);

            $table->foreign('model_id')->references('id')->on('ngn_models');
            $table->foreign('category_id')->references('id')->on('ngn_categories');
            $table->foreign('brand_id')->references('id')->on('ngn_brands');
            // product_type_id foreign key removed - ngn_product_types table was deleted

            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_products');
    }
};
