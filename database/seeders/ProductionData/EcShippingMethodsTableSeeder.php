<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcShippingMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ec_shipping_methods
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ec_shipping_methods`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ec_shipping_methods` (`id`, `name`, `slug`, `logo`, `link_url`, `description`, `shipping_amount`, `is_enabled`, `in_store_pickup`, `created_at`, `updated_at`) VALUES
('1', 'Store Pickup', 'store-pickup', '-', '-', '-', '0.00', '1', '1', NULL, NULL),
('2', 'Delivery', 'delivery', '-', '-', '-', '0.00', '0', '0', NULL, NULL),
('3', 'Partner Store Pickup', 'partner-pickup', '-', '-', '-', '0.00', '1', '1', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
