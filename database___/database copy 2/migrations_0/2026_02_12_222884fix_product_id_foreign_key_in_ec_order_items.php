<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if foreign key exists before trying to drop it
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'ec_order_items' 
            AND CONSTRAINT_NAME = 'ec_order_items_product_id_foreign'
        ");
        
        if (!empty($foreignKeys)) {
            Schema::table('ec_order_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
            });
        }

        Schema::table('ec_order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('ngn_products')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('ec_order_items', function (Blueprint $table) {

            $table->dropForeign(['product_id']);

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('restrict');
        });
    }
};
