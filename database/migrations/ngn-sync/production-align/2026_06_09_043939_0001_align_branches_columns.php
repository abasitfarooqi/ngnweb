<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('branches')) {
            return;
        }

        if (! Schema::hasColumn('branches', 'opening_hours')) {
            DB::statement('ALTER TABLE `branches` ADD COLUMN `opening_hours` text COLLATE utf8mb4_unicode_ci AFTER `city`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
