<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('renting_other_charges')) {
            return;
        }

        if (! Schema::hasColumn('renting_other_charges', 'is_whatsapp_sent')) {
            DB::statement('ALTER TABLE `renting_other_charges` ADD COLUMN `is_whatsapp_sent` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_paid`');
        }

        if (! Schema::hasColumn('renting_other_charges', 'whatsapp_last_reminder_sent_at')) {
            DB::statement('ALTER TABLE `renting_other_charges` ADD COLUMN `whatsapp_last_reminder_sent_at` timestamp NULL DEFAULT NULL AFTER `is_whatsapp_sent`');
        }

        if (! Schema::hasColumn('renting_other_charges', 'email_last_reminder_sent_at')) {
            DB::statement('ALTER TABLE `renting_other_charges` ADD COLUMN `email_last_reminder_sent_at` timestamp NULL DEFAULT NULL AFTER `whatsapp_last_reminder_sent_at`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
