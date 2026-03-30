<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ec_order_items') || ! Schema::hasColumn('ec_order_items', 'product_id')) {
            return;
        }

        DB::statement('ALTER TABLE `ec_order_items` DROP FOREIGN KEY `ec_order_items_product_id_foreign`');
        DB::statement('ALTER TABLE `ec_order_items` MODIFY `product_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `ec_order_items` ADD CONSTRAINT `ec_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ngn_products`(`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('ec_order_items') || ! Schema::hasColumn('ec_order_items', 'product_id')) {
            return;
        }

        DB::statement('ALTER TABLE `ec_order_items` DROP FOREIGN KEY `ec_order_items_product_id_foreign`');
        DB::statement('ALTER TABLE `ec_order_items` MODIFY `product_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `ec_order_items` ADD CONSTRAINT `ec_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ngn_products`(`id`)');
    }
};
