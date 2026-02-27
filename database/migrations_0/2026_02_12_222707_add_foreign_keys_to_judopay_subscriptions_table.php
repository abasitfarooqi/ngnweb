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
        Schema::table('judopay_subscriptions', function (Blueprint $table) {
            $table->foreign(['judopay_onboarding_id'])->references(['id'])->on('judopay_onboardings')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('judopay_subscriptions', function (Blueprint $table) {
            $table->dropForeign('judopay_subscriptions_judopay_onboarding_id_foreign');
        });
    }
};
