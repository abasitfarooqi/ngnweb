<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        if (! Schema::hasColumn('payments', 'pcn_case_id')) {
            DB::statement('ALTER TABLE `payments` ADD COLUMN `pcn_case_id` bigint unsigned DEFAULT NULL AFTER `user_id`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
