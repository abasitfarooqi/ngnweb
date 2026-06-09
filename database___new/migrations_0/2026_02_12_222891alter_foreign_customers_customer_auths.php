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
        // Drop existing foreign key constraints
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('payments_paypal', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        // Add new foreign key constraints
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customer_auths');
        });

        Schema::table('payments_paypal', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customer_auths');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('payments_paypal', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }
};
