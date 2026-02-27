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
        Schema::table('order_shippings', function (Blueprint $table) {
            $table->foreign(['carrier_id'])->references(['id'])->on('carriers')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_shippings', function (Blueprint $table) {
            $table->dropForeign('order_shippings_carrier_id_foreign');
            $table->dropForeign('order_shippings_order_id_foreign');
        });
    }
};
