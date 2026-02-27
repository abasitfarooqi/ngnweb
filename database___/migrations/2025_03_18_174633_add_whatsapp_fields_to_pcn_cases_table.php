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
            // Add nullable boolean field for WhatsApp sent status (0/1)
            $table->boolean('is_whatsapp_sent')->default(0)->nullable();

            // Add nullable datetime field for the last WhatsApp reminder sent time
            $table->dateTime('whatsapp_last_reminder_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->dropColumn('is_whatsapp_sent');
            $table->dropColumn('whatsapp_last_reminder_sent_at');
        });
    }
};
