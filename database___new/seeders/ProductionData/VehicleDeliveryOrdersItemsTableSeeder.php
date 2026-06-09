<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleDeliveryOrdersItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: vehicle_delivery_orders_items
     * Records: 12
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `vehicle_delivery_orders_items`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `vehicle_delivery_orders_items` (`id`, `vehicle_delivery_order_id`, `pickup_point_coordinates_lat`, `pickup_point_coordinates_lon`, `drop_branch_id`, `created_at`, `updated_at`) VALUES
('1', '1', '51.4249099', '-0.0511200', '2', '2024-12-06 15:40:59', '2024-12-06 15:40:59'),
('2', '2', '51.4249099', '-0.0511200', '3', '2024-12-06 15:59:39', '2024-12-06 15:59:39'),
('3', '3', '51.4249099', '-0.0511200', '3', '2024-12-06 16:03:36', '2024-12-06 16:03:36'),
('4', '4', '51.5896250', '-0.2298929', '3', '2024-12-06 16:24:01', '2024-12-06 16:24:01'),
('5', '5', '51.4730158', '-0.0176604', '1', '2024-12-06 23:17:02', '2024-12-06 23:17:02'),
('6', '6', '51.4730158', '-0.0176604', '1', '2024-12-06 23:17:15', '2024-12-06 23:17:15'),
('7', '7', '51.4730158', '-0.0176604', '1', '2024-12-06 23:17:17', '2024-12-06 23:17:17'),
('8', '8', '51.5896250', '-0.2298929', '3', '2024-12-07 12:11:30', '2024-12-07 12:11:30'),
('9', '9', '51.5896250', '-0.2298929', '1', '2024-12-07 12:14:54', '2024-12-07 12:14:54'),
('10', '10', '51.5896250', '-0.2298929', '2', '2024-12-07 12:21:30', '2024-12-07 12:21:30'),
('11', '11', '51.5799800', '0.1242600', '1', '2024-12-07 12:28:18', '2024-12-07 12:28:18'),
('12', '12', '51.4249099', '-0.0511200', '1', '2024-12-16 16:52:56', '2024-12-16 16:52:56');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
