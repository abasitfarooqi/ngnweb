<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->boolean('is_whatsapp_sent')->default(0)->nullable();
            $table->dateTime('whatsapp_last_reminder_sent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {
            $table->dropColumn('is_whatsapp_sent');
            $table->dropColumn('whatsapp_last_reminder_sent_at');
        });
    }
};
