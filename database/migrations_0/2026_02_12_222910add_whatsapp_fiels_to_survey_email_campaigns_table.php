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
        Schema::table('survey_email_campaigns', function (Blueprint $table) {
            $table->boolean('is_whatsapp_sent')->default(false);
            $table->dateTime('last_whatsapp_sent_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_email_campaigns', function (Blueprint $table) {
            $table->dropColumn('is_whatsapp_sent');
            $table->dropColumn('last_whatsapp_sent_datetime');
        });
    }
};
