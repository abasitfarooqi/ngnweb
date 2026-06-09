<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('booking_invoices', 'is_whatsapp_sent')) {
                $table->boolean('is_whatsapp_sent')->nullable()->default(false)->after('deposit');
            }

            if (!Schema::hasColumn('booking_invoices', 'whatsapp_last_reminder_sent_at')) {
                $table->dateTime('whatsapp_last_reminder_sent_at')->nullable()->after('is_whatsapp_sent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_invoices', function (Blueprint $table) {

            if (Schema::hasColumn('booking_invoices', 'whatsapp_last_reminder_sent_at')) {
                $table->dropColumn('whatsapp_last_reminder_sent_at');
            }

            if (Schema::hasColumn('booking_invoices', 'is_whatsapp_sent')) {
                $table->dropColumn('is_whatsapp_sent');
            }
        });
    }
};
