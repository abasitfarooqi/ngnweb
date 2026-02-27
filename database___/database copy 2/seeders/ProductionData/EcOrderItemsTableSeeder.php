<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcOrderItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ec_order_items
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ec_order_items`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ec_order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `quantity`, `unit_price`, `total_price`, `discount`, `tax`, `line_total`, `created_at`, `updated_at`) VALUES
('13', '5006', '1', 'Delivery Bag NGN', 'ngn-delivery-bag', '2', '19.99', '39.98', '0.00', '6.66', '39.98', '2025-06-07 06:09:32', '2025-06-07 06:22:23'),
('14', '5007', '2635', 'Phone Holder With Charger', 'TQ004', '77', '30.00', '2310.00', '0.00', '385.00', '2310.00', '2025-06-07 17:19:37', '2025-06-07 17:32:25'),
('15', '5007', '16', 'Easyblock SH 125 2013-2025', 'EBH01', '2', '180.00', '360.00', '0.00', '60.00', '360.00', '2025-06-07 17:32:49', '2025-06-07 17:32:54');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
