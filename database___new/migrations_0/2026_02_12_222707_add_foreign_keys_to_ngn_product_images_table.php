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
        Schema::table('ngn_product_images', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['id'])->on('ngn_products')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_product_images', function (Blueprint $table) {
            $table->dropForeign('ngn_product_images_product_id_foreign');
        });
    }
};
