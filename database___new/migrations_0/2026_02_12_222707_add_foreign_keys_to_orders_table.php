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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign(['parent_order_id'])->references(['id'])->on('orders')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['payment_method_id'])->references(['id'])->on('payment_methods')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['shipping_address_id'])->references(['id'])->on('user_addresses')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['user_id'])->references(['id'])->on('users-old')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_parent_order_id_foreign');
            $table->dropForeign('orders_payment_method_id_foreign');
            $table->dropForeign('orders_shipping_address_id_foreign');
            $table->dropForeign('orders_user_id_foreign');
        });
    }
};
