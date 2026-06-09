<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('document_types')) {
            return;
        }

        if (! Schema::hasColumn('document_types', 'is_mandatory')) {
            DB::statement('ALTER TABLE `document_types` ADD COLUMN `is_mandatory` tinyint(1) NOT NULL DEFAULT \'0\' AFTER `description`');
        }
        if (! Schema::hasColumn('document_types', 'required_for')) {
            DB::statement('ALTER TABLE `document_types` ADD COLUMN `required_for` json DEFAULT NULL AFTER `is_mandatory`');
        }
        if (! Schema::hasColumn('document_types', 'slug')) {
            DB::statement('ALTER TABLE `document_types` ADD COLUMN `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `name`');
        }
        if (! Schema::hasColumn('document_types', 'sort_order')) {
            DB::statement('ALTER TABLE `document_types` ADD COLUMN `sort_order` int NOT NULL DEFAULT \'0\' AFTER `validation_rules`');
        }
        if (! Schema::hasColumn('document_types', 'validation_rules')) {
            DB::statement('ALTER TABLE `document_types` ADD COLUMN `validation_rules` json DEFAULT NULL AFTER `required_for`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
