<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ngn_attributes', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropPrimary(['product_id', 'attribute_key']);
            $table->integer('stock_in_hand')->nullable();
            $table->primary(['product_id', 'attribute_key', 'attribute_value']);
            $table->foreign('product_id')
                ->references('id')
                ->on('ngn_products')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('ngn_attributes', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropPrimary(['product_id', 'attribute_key', 'attribute_value']);
            $table->dropColumn('stock_in_hand');
            $table->primary(['product_id', 'attribute_key']);
            $table->foreign('product_id')
                ->references('id')
                ->on('ngn_products')
                ->onDelete('cascade');
        });
    }
};
