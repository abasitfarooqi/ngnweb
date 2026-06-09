<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleDeliverySurchargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: vehicle_delivery_surcharges
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `vehicle_delivery_surcharges`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `vehicle_delivery_surcharges` (`id`, `type`, `percentage`, `created_at`, `updated_at`) VALUES
('1', 'Time Surcharge - Peak Hours (7 AM - 9 AM, 5 PM - 8 PM)', '20.00', '2024-12-05 15:07:06', '2024-12-05 15:07:06'),
('2', 'Time Surcharge - Night-Time (9 PM - 7 AM)', '25.00', '2024-12-05 15:07:06', '2024-12-05 15:07:06'),
('3', 'Weekend or Public Holiday Surcharge', '10.00', '2024-12-05 15:07:06', '2024-12-05 15:07:06');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
