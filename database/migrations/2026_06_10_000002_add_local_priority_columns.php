<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branches') && ! Schema::hasColumn('branches', 'opening_hours')) {
            DB::statement('ALTER TABLE `branches` ADD COLUMN `opening_hours` text COLLATE utf8mb4_unicode_ci AFTER `city`');
        }

        if (Schema::hasTable('club_members') && ! Schema::hasColumn('club_members', 'customer_id')) {
            DB::statement('ALTER TABLE `club_members` ADD COLUMN `customer_id` bigint unsigned DEFAULT NULL AFTER `user_id`');
        }

        if (Schema::hasTable('customers') && ! Schema::hasColumn('customers', 'is_club')) {
            DB::statement('ALTER TABLE `customers` ADD COLUMN `is_club` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_register`');
        }

        if (Schema::hasTable('document_types')) {
            if (! Schema::hasColumn('document_types', 'is_mandatory')) {
                DB::statement('ALTER TABLE `document_types` ADD COLUMN `is_mandatory` tinyint(1) NOT NULL DEFAULT 0 AFTER `description`');
            }
            if (! Schema::hasColumn('document_types', 'required_for')) {
                DB::statement('ALTER TABLE `document_types` ADD COLUMN `required_for` json DEFAULT NULL AFTER `is_mandatory`');
            }
            if (! Schema::hasColumn('document_types', 'validation_rules')) {
                DB::statement('ALTER TABLE `document_types` ADD COLUMN `validation_rules` json DEFAULT NULL AFTER `required_for`');
            }
            if (! Schema::hasColumn('document_types', 'slug')) {
                DB::statement('ALTER TABLE `document_types` ADD COLUMN `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `name`');
            }
            if (! Schema::hasColumn('document_types', 'sort_order')) {
                DB::statement('ALTER TABLE `document_types` ADD COLUMN `sort_order` int NOT NULL DEFAULT 0 AFTER `validation_rules`');
            }
        }

        if (Schema::hasTable('ec_order_items')) {
            if (! Schema::hasColumn('ec_order_items', 'item_type')) {
                DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `item_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'catalogue\' AFTER `product_id`');
            }
            if (! Schema::hasColumn('ec_order_items', 'part_number')) {
                DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `part_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `sku`');
            }
            if (! Schema::hasColumn('ec_order_items', 'sp_part_id')) {
                DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `sp_part_id` bigint unsigned DEFAULT NULL AFTER `part_number`');
            }
            if (! Schema::hasColumn('ec_order_items', 'sp_assembly_id')) {
                DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `sp_assembly_id` bigint unsigned DEFAULT NULL AFTER `sp_part_id`');
            }
            if (! Schema::hasColumn('ec_order_items', 'source_meta')) {
                DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `source_meta` json DEFAULT NULL AFTER `line_total`');
            }
        }

        if (Schema::hasTable('payments') && ! Schema::hasColumn('payments', 'pcn_case_id')) {
            DB::statement('ALTER TABLE `payments` ADD COLUMN `pcn_case_id` bigint unsigned DEFAULT NULL AFTER `user_id`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
