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
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->boolean('is_subscription')->default(false)->after('is_used_latest');
            $table->string('subscription_option', 10)->nullable()->after('is_subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn(['is_subscription', 'subscription_option']);
        });
    }
};
