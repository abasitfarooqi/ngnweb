<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ngn_products')) {
            Schema::create('ngn_products', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->string('sku')->unique();
                $table->string('ean')->nullable();
                $table->string('image_url')->nullable();

                $table->string('name');
                $table->string('variation')->nullable();

                $table->text('description')->nullable();
                $table->text('extended_description')->nullable();

                $table->string('colour')->nullable()->default('');

                $table->string('pos_variant_id')->nullable();
                $table->string('pos_product_id')->nullable();

                $table->unsignedBigInteger('brand_id')->index();
                $table->unsignedBigInteger('category_id')->index();
                $table->unsignedBigInteger('model_id')->index();

                $table->decimal('normal_price', 10, 2)->default(0);
                $table->decimal('pos_price', 10, 2)->default(0);
                $table->decimal('pos_vat', 10, 2)->default(0);
                $table->decimal('global_stock', 10, 2)->default(0);

                $table->boolean('vatable')->default(false);
                $table->boolean('is_oxford')->default(false);
                $table->boolean('dead')->default(false);

                $table->string('sorting_code')->nullable()->default('0');

                $table->timestamps();

                $table->string('slug')->default('');
                $table->string('meta_title')->default('');
                $table->text('meta_description')->nullable();
                $table->boolean('is_ecommerce')->default(true);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_products');
    }
};
