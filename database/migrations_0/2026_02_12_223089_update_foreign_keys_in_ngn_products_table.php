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
        // Schema::table('ngn_products', function (Blueprint $table) {
        //     // Change column types to unsignedBigInteger
        //     $table->unsignedBigInteger('model_id')->nullable()->change();
        //     $table->unsignedBigInteger('category_id')->nullable()->change();
        //     $table->unsignedBigInteger('brand_id')->nullable()->change();
        //     $table->unsignedBigInteger('product_type_id')->nullable()->change();
        // });

        // Schema::table('ngn_products', function (Blueprint $table) {
        //     // Re-add foreign key constraints
        //     $table->foreign('model_id')->references('id')->on('ngn_models')->onDelete('set null');
        //     $table->foreign('category_id')->references('id')->on('ngn_categories')->onDelete('set null');
        //     $table->foreign('brand_id')->references('id')->on('ngn_brands')->onDelete('set null');
        //     $table->foreign('product_type_id')->references('id')->on('ngn_product_types')->onDelete('set null');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('ngn_products', function (Blueprint $table) {
            // Change column types back to integer
            $table->integer('model_id')->nullable()->change();
            $table->integer('category_id')->nullable()->change();
            $table->integer('brand_id')->nullable()->change();
            $table->integer('product_type_id')->nullable()->change();
        });

        Schema::table('ngn_products', function (Blueprint $table) {
            // Re-add foreign key constraints
            $table->foreign('model_id')->references('id')->on('ngn_models')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('ngn_categories')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('ngn_brands')->onDelete('set null');
            $table->foreign('product_type_id')->references('id')->on('ngn_product_types')->onDelete('set null');
        });
    }
};
