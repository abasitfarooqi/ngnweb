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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->longText('description')->nullable();
            $table->integer('security_stock')->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->integer('old_price_amount')->nullable();
            $table->integer('price_amount')->nullable();
            $table->integer('cost_amount')->nullable();
            $table->enum('type', ['deliverable', 'downloadable'])->nullable();
            $table->boolean('backorder')->default(false);
            $table->boolean('requires_shipping')->default(false);
            $table->dateTime('published_at')->nullable()->default('2023-04-10 14:45:19');
            $table->string('seo_title', 60)->nullable();
            $table->string('seo_description', 160)->nullable();
            $table->decimal('weight_value', 10, 5)->unsigned()->nullable()->default(0);
            $table->string('weight_unit')->default('kg');
            $table->decimal('height_value', 10, 5)->unsigned()->nullable()->default(0);
            $table->string('height_unit')->default('cm');
            $table->decimal('width_value', 10, 5)->unsigned()->nullable()->default(0);
            $table->string('width_unit')->default('cm');
            $table->decimal('depth_value', 10, 5)->unsigned()->nullable()->default(0);
            $table->string('depth_unit')->default('cm');
            $table->decimal('volume_value', 10, 5)->unsigned()->nullable()->default(0);
            $table->string('volume_unit')->default('l');
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedBigInteger('brand_id')->nullable()->index();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->text('image')->nullable();
            $table->text('images')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
