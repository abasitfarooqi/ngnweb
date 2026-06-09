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
        Schema::table('ngn_stock_movements', function (Blueprint $table) {
            $table->foreign(['branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['product_id'])->references(['id'])->on('ngn_products')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_stock_movements', function (Blueprint $table) {
            $table->dropForeign('ngn_stock_movements_branch_id_foreign');
            $table->dropForeign('ngn_stock_movements_product_id_foreign');
            $table->dropForeign('ngn_stock_movements_user_id_foreign');
        });
    }
};
