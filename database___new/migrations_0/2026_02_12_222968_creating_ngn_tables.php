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
        // Recreate ngn_brands table
        Schema::create('ngn_brands', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Basic brand information
            $table->string('name')->unique();
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            
            // SEO and URL handling
            $table->string('slug')->default('');
            
            // E-commerce functionality
            $table->boolean('is_ecommerce')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            // SEO meta fields
            $table->string('meta_title')->default('');
            $table->text();
            
            // Timestamps
            $table->timestamps();
        });

        // Recreate ngn_categories table
        Schema::create('ngn_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        // Recreate ngn_models table
        Schema::create('ngn_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image_url')->nullable();
        $table->timestamps();
        });

        // Recreate ngn_products table
        Schema::create('ngn_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('ean')->nullable();
            $table->string('image_url')->nullable();
            $table->string('name'); // Product Name
            $table->string('variation')->nullable(); // Product Variation XL, L, M, S, etc.
            $table->text('description')->nullable();   // Product Description
            $table->text('extended_description')->nullable();
            $table->string('colour')->nullable()->default('');
            $table->string('pos_variant_id')->nullable();
            $table->string('pos_product_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable(false);
            $table->foreign('brand_id')->references('id')->on('ngn_brands')->onDelete('restrict');
            $table->unsignedBigInteger('category_id')->nullable(false);
            $table->foreign('category_id')->references('id')->on('ngn_categories')->onDelete('restrict');
            $table->unsignedBigInteger('model_id')->nullable(false);
            $table->foreign('model_id')->references('id')->on('ngn_models')->onDelete('restrict');
            $table->decimal('normal_price', 10, 2)->default(0.00); // Price
            $table->decimal('pos_price', 10, 2)->default(0.00); // POS Price
            $table->decimal('pos_vat', 10, 2)->default(0.00); // POS Vat
            $table->decimal('global_stock', 10, 2)->default(0.00); // Global Stock overall branches
            $table->boolean('vatable')->default(false);
            $table->boolean('is_oxford')->default(false); // If Data from OxfordProducts
            $table->boolean('dead')->default(false); // NGN Product is dead
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_products');
        Schema::dropIfExists('ngn_models');
        Schema::dropIfExists('ngn_categories');
        Schema::dropIfExists('ngn_brands');
    }
};
