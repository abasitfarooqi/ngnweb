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
        Schema::table('ec_order_shippings', function (Blueprint $table) {
            $table->foreign(['order_id'])->references(['id'])->on('ec_orders')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_order_shippings', function (Blueprint $table) {
            $table->dropForeign('ec_order_shippings_order_id_foreign');
        });
    }
};
