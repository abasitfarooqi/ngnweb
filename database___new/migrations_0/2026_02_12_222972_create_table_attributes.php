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
            $table->foreignId('product_id')->constrained('ngn_products')->onDelete('restrict');
            $table->string('attribute_key');
            $table->string('attribute_value');
            $table->primary(['product_id', 'attribute_key']);
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_attributes');
    }
};
