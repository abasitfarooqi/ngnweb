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
        Schema::table('judopay_cit_accesses', function (Blueprint $table) {
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['subscription_id'])->references(['id'])->on('judopay_subscriptions')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('judopay_cit_accesses', function (Blueprint $table) {
            $table->dropForeign('judopay_cit_accesses_customer_id_foreign');
            $table->dropForeign('judopay_cit_accesses_subscription_id_foreign');
        });
    }
};
