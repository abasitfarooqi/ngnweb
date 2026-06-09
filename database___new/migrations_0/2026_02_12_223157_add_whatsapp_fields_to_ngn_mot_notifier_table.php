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
        Schema::table('ngn_mot_notifier', function (Blueprint $table) {
            $table->date('mot_last_whatsapp_notification_date')->nullable()->after('mot_last_phone_notification_date');
            $table->boolean('mot_is_whatsapp_sent')->default(false)->after('mot_is_phone_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_mot_notifier', function (Blueprint $table) {
            $table->dropColumn('mot_last_whatsapp_notification_date');
            $table->dropColumn('mot_is_whatsapp_sent');
        });
    }
};
