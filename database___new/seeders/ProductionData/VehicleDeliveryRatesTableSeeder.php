<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleDeliveryRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: vehicle_delivery_rates
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `vehicle_delivery_rates`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `vehicle_delivery_rates` (`id`, `base_fee`, `per_mile_fee`, `base_distance`, `created_at`, `updated_at`) VALUES
('1', '20.00', '1.50', '5.00', '2024-12-05 15:07:06', '2024-12-05 15:07:06');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
