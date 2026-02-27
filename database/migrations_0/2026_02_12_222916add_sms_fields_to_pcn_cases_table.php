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
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->boolean('is_sms_sent')->default(false);
            $table->timestamp('sms_last_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->dropColumn('is_sms_sent');
            $table->dropColumn('sms_last_sent_at');
        });
    }
};
