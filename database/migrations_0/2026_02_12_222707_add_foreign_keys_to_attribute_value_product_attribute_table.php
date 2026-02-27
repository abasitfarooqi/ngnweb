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
        Schema::table('attribute_value_product_attribute', function (Blueprint $table) {
            $table->foreign(['attribute_value_id'])->references(['id'])->on('attribute_values')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['product_attribute_id'])->references(['id'])->on('product_attributes')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_value_product_attribute', function (Blueprint $table) {
            $table->dropForeign('attribute_value_product_attribute_attribute_value_id_foreign');
            $table->dropForeign('attribute_value_product_attribute_product_attribute_id_foreign');
        });
    }
};
