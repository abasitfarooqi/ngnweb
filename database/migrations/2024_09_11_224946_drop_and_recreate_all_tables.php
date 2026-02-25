<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAndRecreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop all tables if they exist
        Schema::dropIfExists('ngn_stock');
        Schema::dropIfExists('ngn_branch');
        Schema::dropIfExists('ngn_products');
        Schema::dropIfExists('ngn_models');
        Schema::dropIfExists('ngn_categories');
        // ngn_brands table handled by separate clean migration

        // Recreate ngn_categories table
        Schema::create('ngn_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Recreate ngn_models table
        Schema::create('ngn_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Recreate ngn_products table
        Schema::create('ngn_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('image_url')->nullable(); // Adjust this if you need to store multiple images
            $table->string('super_product_name');
            $table->text('description')->nullable();
            $table->text('extended_description')->nullable();
            $table->string('variation')->nullable();
            $table->foreignId('brand_id')->constrained('ngn_brands')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('ngn_categories')->onDelete('cascade');
            $table->foreignId('model_id')->constrained('ngn_models')->onDelete('cascade');
            $table->string('colour')->nullable();
            $table->string('ean')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('rrp_less_vat', 10, 2);
            $table->decimal('rrp_inc_vat', 10, 2);
            $table->boolean('vatable')->default(false);
            $table->integer('stock')->default(0);
            $table->integer('catford_stock')->default(0);
            $table->integer('global_stock')->default(0); // Added global_stock field
            $table->boolean('obsolete')->default(false);
            $table->boolean('dead')->default(false);
            $table->timestamps();
        });

        // Recreate ngn_branch table
        Schema::create('ngn_branch', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->timestamps();
        });

        // Recreate ngn_stock table
        Schema::create('ngn_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('sku');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('ngn_branch')->onDelete('cascade');
            $table->unique(['branch_id', 'sku']); // Ensure unique SKU per branch
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop all tables if they exist
        Schema::dropIfExists('ngn_stock');
        Schema::dropIfExists('ngn_branch');
        Schema::dropIfExists('ngn_products');
        Schema::dropIfExists('ngn_models');
        Schema::dropIfExists('ngn_categories');
        Schema::dropIfExists('ngn_brands');
    }
}
