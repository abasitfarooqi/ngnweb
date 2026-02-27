<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleDeliveryOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: vehicle_delivery_orders
     * Records: 12
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `vehicle_delivery_orders`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `vehicle_delivery_orders` (`id`, `quote_date`, `pickup_date`, `vrm`, `full_name`, `phone_number`, `total_distance`, `surcharge`, `delivery_vehicle_type_id`, `branch_id`, `user_id`, `created_at`, `updated_at`, `notes`, `email`) VALUES
('1', '2024-12-08 03:32:40', '2024-12-08 03:32:40', 'KX67XEL', 'Muhammad Shariq Ayaz', '07723234526', '6.52', '7.80', '3', '2', '93', '2024-12-06 15:40:59', '2024-12-06 15:40:59', '600cc plus', 'gr8shariq@gmail.com'),
('2', '2024-12-06 17:41:00', '2024-12-06 17:41:00', 'KX67XEZ', 'Admin', '+447951790565', '10.28', '5.58', '1', '3', '93', '2024-12-06 15:59:39', '2024-12-06 15:59:39', '125cc', 'gr8shariq@gmail.com'),
('3', '2024-12-08 15:00:55', '2024-12-08 15:00:55', 'KX67XEL', 'Thiago', '+447951790565', '10.27', '2.79', '2', '3', '93', '2024-12-06 16:03:36', '2024-12-06 16:03:36', 'mid range 200cc', 'thiago@neguinhomotors.co.uk'),
('4', '2024-12-06 16:18:39', '2024-12-06 16:18:39', 'KX67XEL', 'Ayaz', '+447951790565', '22.38', '0.00', '3', '3', '93', '2024-12-06 16:24:01', '2024-12-06 16:24:01', '600cc +', 'admin@neguinhomonitors.co.uk'),
('5', '2024-12-07 10:15:45', '2024-12-07 10:15:45', 'AU73ZTL', 'Tahir Shakoor', '07946848097', '3.06', '2.00', '1', '1', '109', '2024-12-06 23:17:02', '2024-12-06 23:17:02', NULL, NULL),
('6', '2024-12-07 10:15:45', '2024-12-07 10:15:45', 'AU73ZTL', 'Tahir Shakoor', '07946848097', '3.06', '2.00', '1', '1', '109', '2024-12-06 23:17:15', '2024-12-06 23:17:15', NULL, NULL),
('7', '2024-12-07 10:15:45', '2024-12-07 10:15:45', 'AU73ZTL', 'Tahir Shakoor', '07946848097', '3.06', '2.00', '1', '1', '109', '2024-12-06 23:17:17', '2024-12-06 23:17:17', NULL, NULL),
('8', '2024-12-07 12:07:01', '2024-12-07 12:07:01', 'KX67XEL', 'Shariq Ayaz', '07723234526', '22.37', '4.61', '1', '3', '93', '2024-12-07 12:11:30', '2024-12-07 12:11:30', '125cc', 'admin@neguinhomotors.co.uk'),
('9', '2024-12-07 12:14:04', '2024-12-07 12:14:04', 'KX67XEL', 'Shariq', '07723234526', '16.22', '3.68', '1', '1', '93', '2024-12-07 12:14:54', '2024-12-07 12:14:54', '125 cc', 'admin@neguinhomotors.co.uk'),
('10', '2024-12-07 23:14:55', '2024-12-07 23:14:55', 'KX67XEL', 'Shariq Ayaz', '07723234526', '14.24', '11.85', '1', '2', '93', '2024-12-07 12:21:30', '2024-12-07 12:21:30', '125 cc', 'admin@neguinhomotors.co.uk'),
('11', '2024-12-07 12:27:11', '2024-12-07 12:27:11', 'AO24PXH', 'Tahir Shakoor', '079468487', '17.36', '3.85', '1', '1', '109', '2024-12-07 12:28:18', '2024-12-07 12:28:18', 'testing', 'tshakoor45@gmail.com'),
('12', '2024-12-16 16:51:40', '2024-12-16 16:51:40', 'KX67XEL', 'Muhammad Shariq Ayaz', '07723234526', '1.91', '0.00', '2', '1', '93', '2024-12-16 16:52:56', '2024-12-16 16:52:56', '200cc', 'a.basit.cse@gmail.com');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
