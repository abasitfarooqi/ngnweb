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
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->foreign(['branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['customer_address_id'])->references(['id'])->on('customer_addresses')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['customer_id'])->references(['id'])->on('customer_auths')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['payment_method_id'])->references(['id'])->on('ec_payment_methods')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['shipping_method_id'])->references(['id'])->on('ec_shipping_methods')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropForeign('ec_orders_branch_id_foreign');
            $table->dropForeign('ec_orders_customer_address_id_foreign');
            $table->dropForeign('ec_orders_customer_id_foreign');
            $table->dropForeign('ec_orders_payment_method_id_foreign');
            $table->dropForeign('ec_orders_shipping_method_id_foreign');
        });
    }
};
