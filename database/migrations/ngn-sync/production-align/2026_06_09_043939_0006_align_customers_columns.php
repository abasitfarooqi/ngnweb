<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        if (! Schema::hasColumn('customers', 'is_club')) {
            DB::statement('ALTER TABLE `customers` ADD COLUMN `is_club` tinyint(1) NOT NULL DEFAULT \'0\' AFTER `is_register`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
