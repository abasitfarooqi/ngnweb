<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ec_orders
     * Records: 8
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ec_orders`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ec_orders` (`id`, `order_date`, `order_status`, `total_amount`, `discount`, `tax`, `grand_total`, `shipping_cost`, `shipping_status`, `shipping_date`, `payment_status`, `currency`, `payment_date`, `payment_reference`, `customer_id`, `shipping_method_id`, `payment_method_id`, `customer_address_id`, `created_at`, `updated_at`, `branch_id`) VALUES
('5000', '2025-01-31 19:24:21', 'Cancelled', '1.24', '0.00', '0.21', '1.24', '0.00', 'Cancelled', NULL, 'Refunded', 'GBP', NULL, '8WT32885EV254882G', '1', '1', '3', '4', '2025-01-31 19:24:21', '2025-02-03 12:13:02', '1'),
('5001', '2025-01-31 20:15:49', 'Cancelled', '1.24', '0.00', '0.21', '1.24', '0.00', 'Cancelled', NULL, 'Refunded', 'GBP', NULL, '0NM13014KM3451422', '1', '1', '3', '4', '2025-01-31 20:15:49', '2025-02-03 14:02:04', '3'),
('5003', '2025-02-03 15:06:24', 'Cancelled', '1.24', '0.00', '0.21', '1.24', '0.00', 'Cancelled', NULL, 'Refunded', 'GBP', NULL, '07C71335PK826415D', '1', '1', '3', '4', '2025-02-03 15:06:24', '2025-02-03 15:30:09', '2'),
('5004', '2025-02-04 15:54:40', 'In Progress', '1.24', '0.00', '0.21', '1.24', '0.00', 'pending', NULL, 'paid', 'GBP', NULL, '312378494C0195331', '1', '1', '3', '4', '2025-02-04 15:54:40', '2025-02-04 15:57:23', '2'),
('5005', '2025-03-27 10:17:35', 'pending', '389.97', '0.00', '65.00', '389.97', '0.00', 'pending', NULL, 'pending', 'GBP', NULL, NULL, '8', '2', '3', '11', '2025-03-27 10:17:35', '2025-03-27 13:18:24', NULL),
('5006', '2025-06-07 05:33:35', 'pending', '39.98', '0.00', '6.66', '39.98', '0.00', 'pending', NULL, 'pending', 'GBP', NULL, NULL, '11', '1', '3', '15', '2025-06-07 05:33:35', '2025-06-07 06:22:23', '1'),
('5007', '2025-06-07 17:19:37', 'pending', '2670.00', '0.00', '445.00', '2670.00', '0.00', 'pending', NULL, 'pending', 'GBP', NULL, NULL, '12', '1', '3', '16', '2025-06-07 17:19:37', '2025-06-07 17:33:02', '1'),
('5008', '2025-08-20 22:40:27', 'pending', '1.24', '0.00', '0.21', '1.24', '0.00', 'pending', NULL, 'pending', 'GBP', NULL, NULL, '16', '1', '3', '20', '2025-08-20 22:40:27', '2025-08-20 22:40:34', '1');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
