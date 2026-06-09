<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ec_order_items')) {
            return;
        }

        if (! Schema::hasColumn('ec_order_items', 'item_type')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `item_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'catalogue\' AFTER `product_id`');
        }
        if (! Schema::hasColumn('ec_order_items', 'part_number')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `part_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `sku`');
        }
        if (! Schema::hasColumn('ec_order_items', 'source_meta')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `source_meta` json DEFAULT NULL AFTER `line_total`');
        }
        if (! Schema::hasColumn('ec_order_items', 'sp_assembly_id')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `sp_assembly_id` bigint unsigned DEFAULT NULL AFTER `sp_part_id`');
        }
        if (! Schema::hasColumn('ec_order_items', 'sp_part_id')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `sp_part_id` bigint unsigned DEFAULT NULL AFTER `part_number`');
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
