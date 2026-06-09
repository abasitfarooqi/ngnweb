<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('club_members')) {
            return;
        }

        if (! Schema::hasColumn('club_members', 'customer_id')) {
            DB::statement('ALTER TABLE `club_members` ADD COLUMN `customer_id` bigint unsigned DEFAULT NULL AFTER `user_id`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
