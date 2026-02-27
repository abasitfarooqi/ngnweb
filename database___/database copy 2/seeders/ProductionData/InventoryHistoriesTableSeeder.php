<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryHistoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: inventory_histories
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `inventory_histories`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `inventory_histories` (`id`, `created_at`, `updated_at`, `stockable_type`, `stockable_id`, `reference_type`, `reference_id`, `quantity`, `old_quantity`, `event`, `description`, `inventory_id`, `user_id`) VALUES
('1', '2023-04-22 16:59:36', '2023-04-22 16:59:36', 'product', '2', NULL, NULL, '1', '1', 'Manually added', NULL, '1', '2'),
('2', '2023-04-24 13:51:37', '2023-04-24 13:51:37', 'product', '3', NULL, NULL, '1', '1', 'Initial inventory', NULL, '1', '2');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
