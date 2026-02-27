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
        Schema::create('ngn_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->string('attribute_key');
            $table->string('attribute_value');
            $table->string('slug');
            $table->timestamps();
            $table->integer('stock_in_hand')->nullable();

            $table->primary(['product_id', 'attribute_key', 'attribute_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_attributes');
    }
};
