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
        Schema::table('ngn_products', function (Blueprint $table) {
            $table->foreign(['brand_id'])->references(['id'])->on('ngn_brands')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['category_id'])->references(['id'])->on('ngn_categories')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['model_id'])->references(['id'])->on('ngn_models')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_products', function (Blueprint $table) {
            $table->dropForeign('ngn_products_brand_id_foreign');
            $table->dropForeign('ngn_products_category_id_foreign');
            $table->dropForeign('ngn_products_model_id_foreign');
        });
    }
};
