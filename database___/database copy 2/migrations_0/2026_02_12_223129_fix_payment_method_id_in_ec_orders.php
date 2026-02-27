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

            // drop payment_method
            $table->dropColumn('payment_method');

            // drop foreign key
            $table->dropForeign(['payment_method_id']);

            // add foreign key
            $table->foreign('payment_method_id')
                ->references('id')
                ->on('ec_payment_methods')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->onDelete('restrict');
        });
    }
};
