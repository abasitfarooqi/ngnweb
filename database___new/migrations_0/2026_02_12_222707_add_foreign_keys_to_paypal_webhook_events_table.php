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
        Schema::table('paypal_webhook_events', function (Blueprint $table) {
            $table->foreign(['payment_id'])->references(['id'])->on('payments_paypal')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paypal_webhook_events', function (Blueprint $table) {
            $table->dropForeign('paypal_webhook_events_payment_id_foreign');
        });
    }
};
